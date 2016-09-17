package vn.piti.draku.piti;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.graphics.Color;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.Toast;

import com.aurelhubert.ahbottomnavigation.AHBottomNavigation;
import com.aurelhubert.ahbottomnavigation.AHBottomNavigationItem;

import org.json.JSONArray;
import org.json.JSONObject;

public class TeacherAttendanceActivity extends AppCompatActivity {
    SessionManager ss;
    AppConfig config;
    ProgressDialog progress;
    public JSONArray attendanceClass;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_attendance_teacher);
        changeColor(getWindow(), getResources().getColor(R.color.material_brown_400));
        ss = new SessionManager(getBaseContext());
        progress = new ProgressDialog(this);
        progress.setMessage("Đang tải danh sách lớp...");
        progress.setProgressStyle(ProgressDialog.STYLE_SPINNER);
        if(isConnected()) {
            progress.show();
            new HttpAsyncTask().execute(config.GET_ATTENDANCE_CLASS);
        }
        else
            Toast.makeText(getBaseContext(),"Không có kết nối Internet", Toast.LENGTH_LONG).show();

    }

    private class HttpAsyncTask extends AsyncTask<String, Void, String> {
        DoPost p = new DoPost();
        @Override
        protected String doInBackground(String... urls) {
            return p.POST(urls[0], String.format("{\"token\":\"%s\"}", ss.getToken()));
        }
        // onPostExecute displays the results of the AsyncTask.
        @Override
        protected void onPostExecute(String result) {
            try {
                JSONObject callbackJson = new JSONObject(result);
                boolean status = callbackJson.getBoolean("status");
                if(status == false){
                    Toast.makeText(getBaseContext(), callbackJson.getString("message"), Toast.LENGTH_LONG).show();
                    Intent goToMainActivity = new Intent(getApplicationContext(), LoginActivity.class);
                    startActivity(goToMainActivity);
                    ss.logout();
                } else {
                    attendanceClass = callbackJson.getJSONArray("class");
                }
                progress.dismiss();
            } catch (Exception e) {
                Log.d("InputStream", e.getLocalizedMessage());
            }
        }
    }

    public boolean isConnected(){
        ConnectivityManager connMgr = (ConnectivityManager) getSystemService(Activity.CONNECTIVITY_SERVICE);
        NetworkInfo networkInfo = connMgr.getActiveNetworkInfo();
        return (networkInfo != null && networkInfo.isConnected());
    }
    public JSONArray getAttendanceClass(){
        return attendanceClass;
    }

    public void changeColor(Window window, int color){
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
            window.setStatusBarColor(color);
        }
    }
}
