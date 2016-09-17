package vn.piti.draku.piti.Teacher;

import android.app.Activity;
import android.content.Intent;
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

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.Calendar;

import vn.piti.draku.piti.AppConfig;
import vn.piti.draku.piti.Tools.ParseInfo;
import vn.piti.draku.piti.R;
import vn.piti.draku.piti.Tools.SessionManager;

public class TeacherScheduleActivity extends Activity{

    AppConfig config;
    SessionManager ss;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.parent_schedule_activity);
        changeColor(getWindow(), getResources().getColor(R.color.material_green_400));
        ss = new SessionManager(getBaseContext());

        findViewById(R.id.backButton).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToMainActivity = new Intent(getApplicationContext(), MainTeacherActivity.class);
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


        float scale = getResources().getDisplayMetrics().density;
        int oneDP = (int) (1*scale + 0.5f);

        TableLayout schedule = (TableLayout) findViewById(R.id.schedule);
        TableRow row;
        TextView column;

        TableLayout.LayoutParams row_layout = new TableLayout.LayoutParams(
                TableLayout.LayoutParams.WRAP_CONTENT, 72*oneDP, 1f);

        TableLayout.LayoutParams row_layout2 = new TableLayout.LayoutParams(
                TableLayout.LayoutParams.WRAP_CONTENT, 48*oneDP, 1f);

        TableRow.LayoutParams column_layout = new TableRow.LayoutParams(
                0, 72*oneDP, 1f);
        TableRow.LayoutParams column_layout2 = new TableRow.LayoutParams(
                0, 48*oneDP, 1f);

        for(int i=0; i<10; i++){
            row = new TableRow(getBaseContext());
            if(i == 0){
                column = new TextView(getBaseContext());
                column.setText("Sáng");
                column.setTextColor(getResources().getColor(R.color.material_grey_700));
                column.setGravity(Gravity.CENTER);
                row.addView(column, column_layout2);
                schedule.addView(row, row_layout2);
            } else if(i == 5){
                column = new TextView(getBaseContext());
                column.setText("Chiều");
                column.setTextColor(getResources().getColor(R.color.material_grey_700));
                column.setGravity(Gravity.CENTER);
                row.addView(column, column_layout2);
                schedule.addView(row, row_layout2);
            }

            row = new TableRow(getBaseContext());
            column = new TextView(getBaseContext());
            column.setBackground(getResources().getDrawable(R.drawable.border_blank));
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
                        column.setBackground(getResources().getDrawable(R.drawable.border_blank));
                        column.setText("Trống");
                        column.setTextColor(getResources().getColor(R.color.material_grey_200));
                    } else if(period.getInt("type") == 2){
                        column.setBackground(getResources().getDrawable(R.drawable.border_success));
                        column.setText(period.getString("name"));
                        column.setTextColor(getResources().getColor(android.R.color.white));
                    } else if(period.getInt("type") == 3){
                        column.setBackground(getResources().getDrawable(R.drawable.border_error));
                        column.setText(period.getString("name"));
                        column.setTextColor(getResources().getColor(android.R.color.white));
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

    public void changeColor(Window window, int color){
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
            window.setStatusBarColor(color);
        }
    }
}
