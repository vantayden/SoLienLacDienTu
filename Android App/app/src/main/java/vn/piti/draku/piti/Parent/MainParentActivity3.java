package vn.piti.draku.piti.Parent;

import android.app.Activity;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.AsyncTask;
import android.os.Bundle;
import android.content.Intent;
import android.os.Handler;
import android.util.Log;
import android.view.View;
import android.widget.TextView;
import android.widget.Toast;

import com.google.firebase.iid.FirebaseInstanceId;
import com.squareup.picasso.Picasso;

import org.json.JSONObject;

import de.hdodenhof.circleimageview.CircleImageView;
import vn.piti.draku.piti.AppConfig;
import vn.piti.draku.piti.Tools.DoPost;
import vn.piti.draku.piti.LoginActivity;
import vn.piti.draku.piti.R;
import vn.piti.draku.piti.Tools.SessionManager;

public class MainParentActivity3 extends Activity  {

    CircleImageView imageView;
    SessionManager ss;
    AppConfig config;
    boolean doubleBackToExitPressedOnce = false;

    @Override
    public void onBackPressed() {
        if (doubleBackToExitPressedOnce) {
            Intent intent = new Intent(Intent.ACTION_MAIN);
            intent.addCategory(Intent.CATEGORY_HOME);
            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
            startActivity(intent);
        }

        this.doubleBackToExitPressedOnce = true;
        Toast.makeText(this, "Press BACK again to exit", Toast.LENGTH_SHORT).show();

        new Handler().postDelayed(new Runnable() {

            @Override
            public void run() {
                doubleBackToExitPressedOnce=false;
            }
        }, 2000);
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        this.ss = new SessionManager(getBaseContext());
        if(!ss.isLoggedIn()){
            Intent goToNextActivity = new Intent(getApplicationContext(), LoginActivity.class);
            startActivity(goToNextActivity);
        } else {
            if(!isConnected())
                Toast.makeText(getBaseContext(), "Không có kết nối mạng để cập nhật!", Toast.LENGTH_SHORT).show();
              else
                new HttpAsyncTask().execute(config.GET_STUDENT_INFO);

            setContentView(R.layout.main3_activity);
            findView();
        }
    }

    private void findView(){
        findViewById(R.id.changeTheme).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToAskActivity = new Intent(getApplicationContext(), MainParentActivity.class);
                startActivity(goToAskActivity);
            }
        });

        findViewById(R.id.main_mark).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToAskActivity = new Intent(getApplicationContext(), ParentMarkActivity.class);
                startActivity(goToAskActivity);
            }
        });

        findViewById(R.id.main_ask).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToAskActivity = new Intent(getApplicationContext(), ParentAskActivity.class);
                startActivity(goToAskActivity);
            }
        });

        findViewById(R.id.main_notification).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToNotificationActivity = new Intent(getApplicationContext(), ParentNotificationActivity.class);
                startActivity(goToNotificationActivity);
            }
        });
        findViewById(R.id.main_student).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToProfileActivity = new Intent(getApplicationContext(), ParentStudentActivity.class);
                startActivity(goToProfileActivity);
            }
        });
        findViewById(R.id.main_schedule).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToScheduleActivity = new Intent(getApplicationContext(), ParentScheduleActivity.class);
                startActivity(goToScheduleActivity);
            }
        });
        imageView = (CircleImageView) findViewById(R.id.student_image);

    }

    private class HttpAsyncTask extends AsyncTask<String, Void, String> {
        DoPost p = new DoPost();
        @Override
        protected String doInBackground(String... urls) {
            return p.POST(urls[0], String.format("{\"token\":\"%s\", \"FCMToken\":\"%s\"}", ss.getToken(), FirebaseInstanceId.getInstance().getToken()));
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
                    ss.setInfo(result);
                    JSONObject student = callbackJson.getJSONObject("student");
                    TextView student_name = (TextView) findViewById(R.id.student_name);
                    student_name.setText(student.getString("name"));
                    TextView student_class = (TextView) findViewById(R.id.student_class);
                    student_class.setText(student.getString("className") + " - " + student.getString("school"));
                    if(!student.getString("image").equals(""))
                        Picasso.with(getBaseContext()).load(config.IMAGE_URL + student.getString("image")).into(imageView);
                }
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
}

