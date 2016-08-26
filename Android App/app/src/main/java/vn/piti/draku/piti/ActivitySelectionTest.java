package vn.piti.draku.piti;

import android.app.Activity;
import android.content.Intent;
import android.graphics.Color;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;
import android.widget.Toast;

import com.aurelhubert.ahbottomnavigation.AHBottomNavigation;
import com.aurelhubert.ahbottomnavigation.AHBottomNavigationItem;

import org.json.JSONArray;
import org.json.JSONObject;

public class ActivitySelectionTest extends AppCompatActivity {
    SessionManager ss;
    AppConfig config;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_notification_teacher);
        createNavigation(4);
        ss = new SessionManager(getBaseContext());
        new HttpAsyncTask().execute(config.GET_TEACHER_CLASS);
    }
    public void createNavigation(int i){
        AHBottomNavigation bottomNavigation = (AHBottomNavigation) findViewById(R.id.bottom_navigation);

        // Create items
        AHBottomNavigationItem item1 = new AHBottomNavigationItem(R.string.teacher_main_notification, R.drawable.ic_notification, R.color.main1);
        AHBottomNavigationItem item2 = new AHBottomNavigationItem(R.string.teacher_main_profile, R.drawable.ic_student, R.color.main2);
        AHBottomNavigationItem item3 = new AHBottomNavigationItem(R.string.teacher_main_mark, R.drawable.ic_mark, R.color.main3);
        AHBottomNavigationItem item4 = new AHBottomNavigationItem(R.string.teacher_main_schedule, R.drawable.ic_schedule, R.color.main4);
        AHBottomNavigationItem item5 = new AHBottomNavigationItem(R.string.teacher_main_attendance, R.drawable.ic_ask, R.color.main5);

        // Add items
        bottomNavigation.addItem(item1);
        bottomNavigation.addItem(item2);
        bottomNavigation.addItem(item3);
        bottomNavigation.addItem(item4);
        bottomNavigation.addItem(item5);

        // Set background color
        bottomNavigation.setDefaultBackgroundColor(Color.parseColor("#FEFEFE"));

        // Disable the translation inside the CoordinatorLayout
        bottomNavigation.setBehaviorTranslationEnabled(false);

        // Change colors
        bottomNavigation.setAccentColor(Color.parseColor("#F63D2B"));
        bottomNavigation.setInactiveColor(Color.parseColor("#747474"));

        // Force to tint the drawable (useful for font with icon for example)
        bottomNavigation.setForceTint(true);

        // Force the titles to be displayed (against Material Design guidelines!)
        bottomNavigation.setForceTitlesDisplay(true);

        // Use colored navigation with circle reveal effect
        bottomNavigation.setColored(true);

        // Set current item programmatically
        bottomNavigation.setCurrentItem(i);

        // Customize notification (title, background, typeface)
        bottomNavigation.setNotificationBackgroundColor(Color.parseColor("#F63D2B"));

        // Add or remove notification for each item
        //bottomNavigation.setNotification("4", 1);
        //bottomNavigation.setNotification("", 1);

        // Set listeners
        bottomNavigation.setOnTabSelectedListener(new AHBottomNavigation.OnTabSelectedListener() {
            @Override
            public boolean onTabSelected(int position, boolean wasSelected) {
                // Do something cool here...
                Intent goToNextActivity;
                if(!wasSelected)
                    switch(position){
                        case 0:
                            goToNextActivity = new Intent(getApplicationContext(), TeacherNotificationActivity.class);
                            startActivity(goToNextActivity);
                            break;
                        case 1:
                            goToNextActivity = new Intent(getApplicationContext(), TeacherProfileActivity.class);
                            startActivity(goToNextActivity);
                            break;
                        case 2:
                            goToNextActivity = new Intent(getApplicationContext(), TeacherMarkActivity.class);
                            startActivity(goToNextActivity);
                            break;
                        case 3:
                            goToNextActivity = new Intent(getApplicationContext(), TeacherScheduleActivity.class);
                            startActivity(goToNextActivity);
                            break;
                        case 4:
                            goToNextActivity = new Intent(getApplicationContext(), TeacherAttendanceActivity.class);
                            startActivity(goToNextActivity);
                            break;
                    }
                return true;
            }
        });
        bottomNavigation.setOnNavigationPositionListener(new AHBottomNavigation.OnNavigationPositionListener() {
            @Override public void onPositionChange(int y) {
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
                    JSONArray myClass = callbackJson.getJSONArray("class");
                    ss.setMyClass(myClass.toString());
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
