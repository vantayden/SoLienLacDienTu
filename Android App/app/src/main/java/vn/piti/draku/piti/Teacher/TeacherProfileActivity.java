package vn.piti.draku.piti.Teacher;

import android.content.DialogInterface;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;
import android.view.View;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.squareup.picasso.Picasso;

import org.json.JSONObject;

import vn.piti.draku.piti.AppConfig;
import vn.piti.draku.piti.Tools.DoPost;
import vn.piti.draku.piti.LoginActivity;
import vn.piti.draku.piti.Tools.ParseInfo;
import vn.piti.draku.piti.R;
import vn.piti.draku.piti.Tools.SessionManager;


public class TeacherProfileActivity extends AppCompatActivity {
    TextView type_school, address, subject, phone;
    ParseInfo info;
    SessionManager ss;
    AppConfig config;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        info = new ParseInfo(getBaseContext());
        setContentView(R.layout.teacher_profile_activity);
        findViewById(R.id.backButton).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToMainActivity = new Intent(getApplicationContext(), MainTeacherActivity.class);
                startActivity(goToMainActivity);
            }
        });
        ss = new SessionManager(getBaseContext());

        try {
            JSONObject teacher = info.getTeacher();
            TextView teacher_name = (TextView) findViewById(R.id.teacher_name);
            teacher_name.setText(teacher.getString("name"));
            type_school = (TextView) findViewById(R.id.teacher_type);
            type_school.setText(teacher.getString("type") + "\n" + teacher.getString("school"));
            subject = (TextView) findViewById(R.id.teacher_subject);
            subject.setText(teacher.getString("subject"));
            address = (TextView) findViewById(R.id.teacher_address);
            address.setText(teacher.getString("address"));
            phone = (TextView) findViewById(R.id.teacher_phone);
            phone.setText(teacher.getString("phone"));

            ImageView imageView = (ImageView) findViewById(R.id.teacher_image);
            if(!teacher.getString("image").equals(""))
                Picasso.with(getBaseContext()).load(config.IMAGE_URL + teacher.getString("image")).into(imageView);
        } catch (Exception e){
            Log.d("InputStream", e.getLocalizedMessage());
        }

        findViewById(R.id.logoutButton).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                new AlertDialog.Builder(TeacherProfileActivity.this)
                        .setTitle("Xác nhận")
                        .setMessage("Bạn có muốn Đăng xuất?")
                        .setPositiveButton("Đăng xuất", new DialogInterface.OnClickListener() {

                            public void onClick(DialogInterface dialog, int whichButton) {
                                AppConfig config = new AppConfig();
                                new HttpAsyncTask().execute(config.LOGOUT_URL);
                            }})
                        .setNegativeButton("Hủy", null).show();

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
                boolean status = callbackJson.getBoolean("status");
                if(status == false){
                    Toast.makeText(getBaseContext(), callbackJson.getString("message"), Toast.LENGTH_LONG).show();
                    ss.setLogin(false);
                    Intent goToNextActivity = new Intent(getApplicationContext(), LoginActivity.class);
                    startActivity(goToNextActivity);
                } else {
                    Intent goToMainActivity = new Intent(getApplicationContext(), LoginActivity.class);
                    startActivity(goToMainActivity);
                    ss.logout();
                }
            } catch (Exception e) {
                Log.d("InputStream", e.getLocalizedMessage());
            }
        }
    }
}
