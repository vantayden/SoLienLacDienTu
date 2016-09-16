package vn.piti.draku.piti;

import android.app.Activity;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.util.Log;
import android.view.View;
import android.widget.TextView;
import android.widget.Toast;

import com.google.firebase.iid.FirebaseInstanceId;

import org.json.JSONObject;

public class MainParentActivity2 extends Activity  {

    SessionManager ss;
    AppConfig config;
    boolean doubleBackToExitPressedOnce = false;

    @Override
    public void onBackPressed() {
        if (doubleBackToExitPressedOnce) {
            super.onBackPressed();
            return;
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

            setContentView(R.layout.new_main2);
            findView();
        }
    }

    private void findView(){
        findViewById(R.id.changeTheme).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToAskActivity = new Intent(getApplicationContext(), MainParentActivity3.class);
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
                    ss.setLogin(false);
                    Intent goToNextActivity = new Intent(getApplicationContext(), LoginActivity.class);
                    startActivity(goToNextActivity);
                } else {
                    ss.setInfo(result);
                    JSONObject student = callbackJson.getJSONObject("student");
                    TextView student_name = (TextView) findViewById(R.id.student_name);
                    student_name.setText(student.getString("name") + " - " + student.getString("className"));
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

