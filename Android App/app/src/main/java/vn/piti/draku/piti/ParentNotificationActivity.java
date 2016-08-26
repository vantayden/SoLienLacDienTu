package vn.piti.draku.piti;

import android.app.Activity;
import android.content.Intent;
import android.graphics.Color;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.Toast;

import com.aurelhubert.ahbottomnavigation.AHBottomNavigation;
import com.aurelhubert.ahbottomnavigation.AHBottomNavigationItem;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;

import mehdi.sakout.dynamicbox.DynamicBox;
import vn.piti.draku.piti.DoiTuong.Notification;

public class ParentNotificationActivity extends Activity{
    DynamicBox box;
    AppConfig config;
    ImageView back, reload;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_notification_parent);
        createNavigation(0);

        ListView lv = (ListView)findViewById(R.id.listView);

        // Setup by Box
        box = new DynamicBox(this,lv); // or new DynamicBox(this,R.id.listView)
        box.setLoadingMessage("Đang tải thông báo...");
        View emptyCollectionView = getLayoutInflater().inflate(R.layout.no_notification, null, false);
        box.addCustomView(emptyCollectionView,"no_notification");
        box.setClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if(!isConnected())
                    box.showInternetOffLayout();
                else
                    new HttpAsyncTask().execute(config.GET_NOTIFICATION);
            }
        });

        back = (ImageView) findViewById(R.id.backButton);
        back.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToMainActivity = new Intent(getApplicationContext(), MainParentActivity.class);
                startActivity(goToMainActivity);
            }
        });

        reload = (ImageView) findViewById(R.id.reloadButton);
        reload.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                box.showLoadingLayout();
                new HttpAsyncTask().execute(config.GET_NOTIFICATION);
            }
        });

        box.showLoadingLayout();

        if(!isConnected())
            box.showInternetOffLayout();
        else
            new HttpAsyncTask().execute(config.GET_NOTIFICATION);
    }

    public void createNavigation(int i){
        AHBottomNavigation bottomNavigation = (AHBottomNavigation) findViewById(R.id.bottom_navigation);

        // Create items
        AHBottomNavigationItem item1 = new AHBottomNavigationItem(R.string.parent_main_notification, R.drawable.ic_notification, R.color.main1);
        AHBottomNavigationItem item2 = new AHBottomNavigationItem(R.string.parent_main_student, R.drawable.ic_student, R.color.main2);
        AHBottomNavigationItem item3 = new AHBottomNavigationItem(R.string.parent_main_mark, R.drawable.ic_mark, R.color.main3);
        AHBottomNavigationItem item4 = new AHBottomNavigationItem(R.string.parent_main_schedule, R.drawable.ic_schedule, R.color.main4);
        AHBottomNavigationItem item5 = new AHBottomNavigationItem(R.string.parent_main_ask, R.drawable.ic_ask, R.color.main5);

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
                            goToNextActivity = new Intent(getApplicationContext(), ParentNotificationActivity.class);
                            startActivity(goToNextActivity);
                            break;
                        case 1:
                            goToNextActivity = new Intent(getApplicationContext(), ParentStudentActivity.class);
                            startActivity(goToNextActivity);
                            break;
                        case 2:
                            goToNextActivity = new Intent(getApplicationContext(), ParentMarkActivity.class);
                            startActivity(goToNextActivity);
                            break;
                        case 3:
                            goToNextActivity = new Intent(getApplicationContext(), ParentScheduleActivity.class);
                            startActivity(goToNextActivity);
                            break;
                        case 4:
                            goToNextActivity = new Intent(getApplicationContext(), ParentAskActivity.class);
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
        SessionManager ss = new SessionManager(getBaseContext());
        @Override
        protected String doInBackground(String... urls) {
            return p.POST(urls[0], String.format("{\"token\":\"%s\"}", ss.getToken()));
        }
        // onPostExecute displays the results of the AsyncTask.
        @Override
        protected void onPostExecute(String result) {
            try {
                JSONObject callbackJson = new JSONObject(result);
                int total = callbackJson.getInt("total");
                if(total == 0)
                    box.showCustomView("no_notification");
                else{
                    JSONArray notifications = callbackJson.getJSONArray("notification");
                    ArrayList<Notification> results = new ArrayList<Notification>();
                    JSONObject single;
                    Notification newsData;
                    for(int i=0; i<total; i++){
                        single = notifications.getJSONObject(i);
                        newsData = new Notification();
                        newsData.setContent(single.getString("content"));
                        newsData.setTeacher(single.getString("teacher"));
                        newsData.setDate(single.getString("date"));
                        results.add(newsData);
                    }
                    final ListView lv1 = (ListView) findViewById(R.id.listView);
                    lv1.setAdapter(new CustomNotificationListAdapter(getBaseContext(), results));
                }
            } catch (Exception e) {
                Log.d("InputStream", e.getLocalizedMessage());
            }
        }
    }
    public boolean isConnected(){
        ConnectivityManager connMgr = (ConnectivityManager) getSystemService(Activity.CONNECTIVITY_SERVICE);
        NetworkInfo networkInfo = connMgr.getActiveNetworkInfo();
        if (networkInfo != null && networkInfo.isConnected())
            return true;
        else
            return false;
    }
}
