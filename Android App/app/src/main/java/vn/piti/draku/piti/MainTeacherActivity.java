package vn.piti.draku.piti;

import android.app.Activity;
import android.content.res.Configuration;
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


import org.json.JSONObject;

public class MainTeacherActivity extends Activity  {

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
                new HttpAsyncTask().execute(config.GET_TEACHER_INFO);
            if(this.getResources().getConfiguration().orientation == Configuration.ORIENTATION_LANDSCAPE)
                setLanscape();
            else
                setPortrait();
        }
    }
    public void onConfigurationChanged (Configuration newConfig){
        int orientation = newConfig.orientation;

        switch(orientation) {

            case Configuration.ORIENTATION_LANDSCAPE:
                setLanscape();
                break;

            case Configuration.ORIENTATION_PORTRAIT:
                setPortrait();
                break;

        }
    }


    public void setLanscape(){
        setContentView(R.layout.activity_main_parent_lanscape);
        findView();
    }

    public void setPortrait(){
        setContentView(R.layout.activity_main_parent);
        findView();
    }

    private void findView(){
        TextView title = (TextView) findViewById(R.id.title1);
        title.setText(R.string.teacher_main_notification);

        title = (TextView) findViewById(R.id.title2);
        title.setText(R.string.teacher_main_profile);
        title = (TextView) findViewById(R.id.title3);
        title.setText(R.string.teacher_main_mark);
        title = (TextView) findViewById(R.id.title4);
        title.setText(R.string.teacher_main_schedule);
        title = (TextView) findViewById(R.id.title5);
        title.setText(R.string.teacher_main_attendance);

        findViewById(R.id.parent_main_mark).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToAskActivity = new Intent(getApplicationContext(), TeacherMarkActivity.class);
                startActivity(goToAskActivity);
            }
        });

        findViewById(R.id.parent_main_ask).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToAskActivity = new Intent(getApplicationContext(), TeacherAttendanceActivity.class);
                startActivity(goToAskActivity);
            }
        });

        findViewById(R.id.parent_main_notification).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToNotificationActivity = new Intent(getApplicationContext(), TeacherNotificationActivity.class);
                startActivity(goToNotificationActivity);
            }
        });
        findViewById(R.id.parent_main_student).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToProfileActivity = new Intent(getApplicationContext(), TeacherProfileActivity.class);
                startActivity(goToProfileActivity);
            }
        });
        findViewById(R.id.parent_main_schedule).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToScheduleActivity = new Intent(getApplicationContext(), TeacherScheduleActivity.class);
                startActivity(goToScheduleActivity);
            }
        });
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
                int code = callbackJson.getInt("code");
                if(code == 0){
                    Toast.makeText(getBaseContext(), callbackJson.getString("message"), Toast.LENGTH_LONG).show();
                } else {
                    ss.setInfo(result);
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

