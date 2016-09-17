package vn.piti.draku.piti.Parent;

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

import vn.piti.draku.piti.AppConfig;
import vn.piti.draku.piti.Tools.DoPost;
import vn.piti.draku.piti.Teacher.FragmentTeacherInfo;
import vn.piti.draku.piti.Tools.ParseInfo;
import vn.piti.draku.piti.R;
import vn.piti.draku.piti.Tools.SessionManager;

public class ParentMarkActivity extends Activity {
    ProgressDialog progress;
    AppConfig config;
    SessionManager ss;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.parent_mark_activity);
        changeColor(getWindow(), getResources().getColor(R.color.material_red_400));
        ss = new SessionManager(getBaseContext());

        findViewById(R.id.backButton).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToMainActivity = new Intent(getApplicationContext(), MainParentActivity.class);
                startActivity(goToMainActivity);
            }
        });

        JSONArray marks = new JSONArray();
        JSONObject mark = new JSONObject();
        String[] markArray = new String[7];
        ParseInfo info = new ParseInfo(getBaseContext());
        try{
            marks = info.getMark();
        }
        catch (Exception e){
            Log.d("InputStream", e.getLocalizedMessage());
        }

        float scale = getResources().getDisplayMetrics().density;
        int oneDP = (int) (1*scale + 0.5f);

        TableLayout table_mark = (TableLayout) findViewById(R.id.table_mark);
        TableRow row;
        TextView column;

        TableLayout.LayoutParams row_layout = new TableLayout.LayoutParams(
                TableLayout.LayoutParams.WRAP_CONTENT, 72*oneDP, 1f);

        TableRow.LayoutParams column_layout = new TableRow.LayoutParams(
                0, 72*oneDP, 1f);

        progress = new ProgressDialog(this);
        progress.setMessage("Đang lấy thông tin giáo viên...");
        progress.setProgressStyle(ProgressDialog.STYLE_SPINNER);

        for(int i=0; i<marks.length(); i++){
            try {
                mark = marks.getJSONObject(i);
                markArray = stringMark(parseMark(mark));
                row = new TableRow(getBaseContext());

                column = new TextView(getBaseContext());
                column.setId(mark.getInt("teacher"));
                column.setBackground(getResources().getDrawable(R.drawable.border_blank));
                column.setGravity(Gravity.CENTER);
                column.setText(mark.getString("name"));
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
                column.setTextColor(getResources().getColor(R.color.material_grey_500));
                row.addView(column, column_layout);

                for(int j=0; j<7; j++){
                    column = new TextView(getBaseContext());
                    column.setGravity(Gravity.CENTER);
                    if(j == 6){
                        column.setBackground(getResources().getDrawable(R.drawable.border_blue));
                        column.setText(markArray[6]);
                        column.setTextColor(getResources().getColor(R.color.white));
                    }
                    else if(Float.parseFloat(markArray[j]) == -1) {
                        column.setBackground(getResources().getDrawable(R.drawable.border_blank));
                        column.setText("Trống");
                        column.setTextColor(getResources().getColor(R.color.material_grey_200));
                    } else if(Float.parseFloat(markArray[j])  >= 8){
                        column.setBackground(getResources().getDrawable(R.drawable.border_success));
                        column.setText(markArray[j]);
                        column.setTextColor(getResources().getColor(R.color.white));
                    } else if(Float.parseFloat(markArray[j])  < 5){
                        column.setBackground(getResources().getDrawable(R.drawable.border_error));
                        column.setText(markArray[j]);
                        column.setTextColor(getResources().getColor(R.color.white));
                    } else {
                        column.setBackground(getResources().getDrawable(R.drawable.border_warning));
                        column.setText(markArray[j]);
                        column.setTextColor(getResources().getColor(R.color.material_grey_500));
                    }
                    row.addView(column, column_layout);
                }

                table_mark.addView(row, row_layout);
            } catch (Exception e){
                Log.d("InputStream", e.getLocalizedMessage());
            }
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

    public float[] parseMark(JSONObject mark){
        float[] markArray = new float[7];
            try {
                for(int j=0; j<mark.getJSONArray("hs1").length(); j++){
                    markArray[j] = Float.parseFloat(mark.getJSONArray("hs1").getJSONObject(j).getString("mark"));
                }

                for(int j=mark.getJSONArray("hs1").length(); j<3; j++){
                    markArray[j] = -1;
                }

                for(int j=3; j < mark.getJSONArray("hs2").length() + 3; j++){
                    markArray[j] = Float.parseFloat(mark.getJSONArray("hs2").getJSONObject(j-3).getString("mark"));
                }

                for(int j = mark.getJSONArray("hs2").length() + 3; j < 5; j++){
                    markArray[j] = -1;
                }

                if(mark.getJSONArray("hs3").length() == 1)
                    markArray[5] = Float.parseFloat(mark.getJSONArray("hs3").getJSONObject(0).getString("mark"));
                else
                    markArray[5] = -1;

                float total = 0;
                int tb=0;
                for(int j=0; j<3; j++){
                    if(markArray[j] != -1) {
                        total += markArray[j];
                        tb++;
                    }
                }

                for(int j=3; j<5; j++){
                    if(markArray[j] != -1) {
                        total += markArray[j]*2;
                        tb += 2;
                    }
                }

                if(markArray[5] != -1) {
                    total += markArray[5]*3;
                    tb += 3;
                }

                markArray[6] = total / tb;

            } catch (Exception e){
                Log.d("InputStream", e.getLocalizedMessage());
            }
        return markArray;
    }

    public String[] stringMark(float[] mark){
        String[] markArray = new String[7];
        for(int i=0; i<7; i++){
            if(i<3 || (float) mark[i]%1 == 0)
                markArray[i] = String.format("%.0f", mark[i]);
            else
                markArray[i] = String.format("%.2f", mark[i]);
        }
        return markArray;
    }

}