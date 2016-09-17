package vn.piti.draku.piti;

import android.app.Activity;
import android.app.FragmentManager;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.Toast;

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
        changeColor(getWindow(), getResources().getColor(R.color.material_yellow_400));

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

        findViewById(R.id.backButton).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToMainActivity = new Intent(getApplicationContext(), MainParentActivity.class);
                startActivity(goToMainActivity);
            }
        });

        findViewById(R.id.reloadButton).setOnClickListener(new View.OnClickListener() {
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
                boolean status = callbackJson.getBoolean("status");
                if(!status) {
                    Toast.makeText(getBaseContext(), callbackJson.getString("message"), Toast.LENGTH_LONG).show();
                    Intent goToMainActivity = new Intent(getApplicationContext(), LoginActivity.class);
                    startActivity(goToMainActivity);
                    ss.logout();
                } else {
                    int total = callbackJson.getInt("total");
                    if(total == 0)
                        box.showCustomView("no_notification");
                    else{
                        JSONArray notifications = callbackJson.getJSONArray("notifications");
                        ArrayList<Notification> results = new ArrayList<Notification>();
                        JSONObject single;
                        Notification newsData;
                        for(int i=0; i<total; i++){
                            single = notifications.getJSONObject(i);
                            newsData = new Notification();
                            newsData.setContent(single.getString("content"));
                            newsData.setTeacher(single.getJSONObject("teacher"));
                            newsData.setDate(single.getString("date"));
                            results.add(newsData);
                        }
                        final ListView lv1 = (ListView) findViewById(R.id.listView);
                        FragmentManager fm = getFragmentManager();
                        lv1.setAdapter(new CustomNotificationListAdapter(getBaseContext(), results, fm));
                    }
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

    public void changeColor(Window window, int color){
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
            window.setStatusBarColor(color);
        }
    }
}
