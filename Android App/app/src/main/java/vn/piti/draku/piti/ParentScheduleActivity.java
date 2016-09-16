package vn.piti.draku.piti;

import android.app.Activity;
import android.app.FragmentManager;
import android.app.ProgressDialog;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.util.Log;
import android.view.Gravity;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;
import android.widget.Toast;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.Calendar;

public class ParentScheduleActivity extends Activity {
    ProgressDialog progress;
    AppConfig config;
    SessionManager ss;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_schedule);
        changeColor(getWindow(), getResources().getColor(R.color.material_green_400));
        ss = new SessionManager(getBaseContext());

        findViewById(R.id.backButton).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToMainActivity = new Intent(getApplicationContext(), MainParentActivity.class);
                startActivity(goToMainActivity);
            }
        });
        TextView thu = new TextView(getBaseContext());
        switch (Calendar.getInstance().get(Calendar.DAY_OF_WEEK)){
            case 2:
                thu = (TextView) findViewById(R.id.t2);
                break;
            case 3:
                thu = (TextView) findViewById(R.id.t3);
                break;
            case 4:
                thu = (TextView) findViewById(R.id.t4);
                break;
            case 5:
                thu = (TextView) findViewById(R.id.t5);
                break;
            case 6:
                thu = (TextView) findViewById(R.id.t6);
                break;
            case 7:
                thu = (TextView) findViewById(R.id.t7);
                break;
            default:
                break;
        }
        thu.setTextColor(getResources().getColor(R.color.white));
        thu.setBackgroundColor(getResources().getColor(R.color.material_grey_500));

        JSONArray schedules = new JSONArray();
        JSONObject period = new JSONObject();
        ParseInfo info = new ParseInfo(getBaseContext());
        try{
            schedules = info.getSchedule();
        }
        catch (Exception e){
            Log.d("InputStream", e.getLocalizedMessage());
        }

        progress = new ProgressDialog(this);
        progress.setMessage("Đang lấy thông tin giáo viên...");
        progress.setProgressStyle(ProgressDialog.STYLE_SPINNER);

        float scale = getResources().getDisplayMetrics().density;
        int oneDP = (int) (1*scale + 0.5f);

        TableLayout schedule = (TableLayout) findViewById(R.id.schedule);
        TableRow row;
        TextView column;

        TableLayout.LayoutParams row_layout = new TableLayout.LayoutParams(
                TableLayout.LayoutParams.WRAP_CONTENT, 55*oneDP, 1f);

        TableRow.LayoutParams column_layout = new TableRow.LayoutParams(
                0, 55*oneDP, 1f);

        for(int i=0; i<10; i++){
            row = new TableRow(getBaseContext());
            if(i == 0){
                column = new TextView(getBaseContext());
                column.setText("Sáng");
                column.setTextColor(getResources().getColor(R.color.material_grey_700));
                column.setGravity(Gravity.CENTER);
                row.addView(column, column_layout);
                schedule.addView(row, row_layout);
            } else if(i == 5){
                column = new TextView(getBaseContext());
                column.setText("Chiều");
                column.setTextColor(getResources().getColor(R.color.material_grey_700));
                column.setGravity(Gravity.CENTER);
                row.addView(column, column_layout);
                schedule.addView(row, row_layout);
            }

            row = new TableRow(getBaseContext());
            column = new TextView(getBaseContext());
            column.setBackground(getResources().getDrawable(R.drawable.border_schedule_blank));
            column.setText("Tiết " + Integer.toString(i+1));
            column.setTextColor(getResources().getColor(R.color.material_grey_700));
            column.setGravity(Gravity.CENTER);
            row.addView(column, column_layout);

            for(int j=0; j<6; j++){
                column = new TextView(getBaseContext());
                try{
                    column.setGravity(Gravity.CENTER);
                    period = schedules.getJSONObject(j).getJSONArray("periods").getJSONObject(i);
                    if(period.getInt("type") == 1) {
                        column.setBackground(getResources().getDrawable(R.drawable.border_schedule_blank));
                        column.setText("Trống");
                        column.setTextColor(getResources().getColor(R.color.material_grey_200));
                    } else if(period.getInt("type") == 2){
                        column.setId(Integer.parseInt(period.getString("teacher")));
                        column.setBackground(getResources().getDrawable(R.drawable.border_schedule_normal));
                        column.setText(period.getString("name"));
                        column.setTextColor(getResources().getColor(android.R.color.white));
                        column.setOnClickListener(new View.OnClickListener() {
                            @Override
                            public void onClick(View v) {
                                if(!isConnected())
                                    Toast.makeText(getBaseContext(), "Không có kết nối mạng", Toast.LENGTH_SHORT).show();
                                else {
                                    progress.show();
                                    TextView per = (TextView) v;
                                    new HttpAsyncTask().execute(config.GET_TEACHER + Integer.toString(per.getId()));
                                }
                            }
                        });
                    } else if(period.getInt("type") == 3){
                        column.setId(Integer.parseInt(period.getString("teacher")));
                        column.setBackground(getResources().getDrawable(R.drawable.border_schedule_test));
                        column.setText(period.getString("name"));
                        column.setTextColor(getResources().getColor(android.R.color.white));
                        column.setOnClickListener(new View.OnClickListener() {
                            @Override
                            public void onClick(View v) {
                                if(!isConnected())
                                    Toast.makeText(getBaseContext(), "Không có kết nối mạng", Toast.LENGTH_SHORT).show();
                                else {
                                    TextView per = (TextView) v;
                                    new HttpAsyncTask().execute(config.GET_TEACHER + Integer.toString(per.getId()));
                                }
                            }
                        });;
                    }
                }
                catch (Exception e){
                    Log.d("InputStream", e.getLocalizedMessage());
                }
                row.addView(column, column_layout);
            }
            schedule.addView(row, row_layout);
        }
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
            progress.dismiss();
            try {
                JSONObject callbackJson = new JSONObject(result);
                boolean status = callbackJson.getBoolean("status");
                if(status == false){
                    Toast.makeText(getBaseContext(), callbackJson.getString("message"), Toast.LENGTH_LONG).show();
                } else {
                    final FragmentTeacherInfo dialogFragment = new FragmentTeacherInfo();
                    FragmentManager fm = getFragmentManager();
                    Bundle teacherInfo = new Bundle();
                    String[] teacherArray = new String[6];
                    JSONObject teacher = callbackJson.getJSONObject("teacher");
                    teacherArray[0] = teacher.getString("name");
                    teacherArray[1] = teacher.getString("image");
                    teacherArray[2] = teacher.getString("type");
                    teacherArray[3] = teacher.getString("subject");
                    teacherArray[4] = teacher.getString("address");
                    teacherArray[5] = teacher.getString("phone");
                    teacherInfo.putStringArray("teacher_info", teacherArray);
                    dialogFragment.setArguments(teacherInfo);
                    dialogFragment.show(fm, "Thông tin giáo viên");
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
    public void changeColor(Window window, int color){
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
            window.setStatusBarColor(color);
        }
    }
}
