package vn.piti.draku.piti.Teacher;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Toast;

import org.json.JSONArray;
import org.json.JSONObject;

import vn.piti.draku.piti.AppConfig;
import vn.piti.draku.piti.Tools.DoPost;
import vn.piti.draku.piti.LoginActivity;
import vn.piti.draku.piti.R;
import vn.piti.draku.piti.Tools.SessionManager;

public class TeacherNotificationActivity extends AppCompatActivity {
    SessionManager ss;
    AppConfig config;
    ProgressDialog progress;
    JSONArray myClass;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.teacher_notification_activity);
        changeColor(getWindow(), getResources().getColor(R.color.material_yellow_400));
        ss = new SessionManager(getBaseContext());
        progress = new ProgressDialog(this);
        progress.setMessage("Đang tải danh sách lớp...");
        progress.setProgressStyle(ProgressDialog.STYLE_SPINNER);
        if(isConnected()) {
            progress.show();
            new HttpAsyncTask().execute(config.GET_TEACHER_CLASS);
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
                    ss.logout();
                    Intent goToNextActivity = new Intent(getApplicationContext(), LoginActivity.class);
                    startActivity(goToNextActivity);
                } else {
                    myClass = callbackJson.getJSONArray("class");
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
    public JSONArray getMyClass(){
        return myClass;
    }

    public void changeColor(Window window, int color){
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
            window.setStatusBarColor(color);
        }
    }
}
