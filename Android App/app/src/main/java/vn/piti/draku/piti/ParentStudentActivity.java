package vn.piti.draku.piti;

import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;
import android.view.View;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.squareup.picasso.Picasso;

import org.json.JSONObject;


public class ParentStudentActivity extends AppCompatActivity {
    TextView class_school, address, dadname, dadphone, momname, momphone;
    ParseInfo info;
    SessionManager ss;
    AppConfig config;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        info = new ParseInfo(getBaseContext());
        setContentView(R.layout.activity_student_parent);
        findViewById(R.id.backButton).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToMainActivity = new Intent(getApplicationContext(), MainParentActivity.class);
                startActivity(goToMainActivity);
            }
        });
        ss = new SessionManager(getBaseContext());

        try {
            JSONObject student = info.getStudentInfo();
            TextView student_name = (TextView) findViewById(R.id.student_name);
            student_name.setText(student.getString("name"));
            class_school = (TextView) findViewById(R.id.class_school);
            class_school.setText(student.getString("className") + "\n" + student.getString("school"));
            address = (TextView) findViewById(R.id.address);
            address.setText(student.getString("address"));
            ImageView imageView = (ImageView) findViewById(R.id.student_image);
            if(!student.getString("image").equals(""))
                Picasso.with(getBaseContext()).load(config.IMAGE_URL + student.getString("image")).into(imageView);

            JSONObject dad = info.getDadInfo();
            dadname = (TextView) findViewById(R.id.dadname);
            dadname.setText(dad.getString("name"));
            dadphone = (TextView) findViewById(R.id.dadphone);
            dadphone.setText(dad.getString("phone"));

            JSONObject mom = info.getMomInfo();
            momname = (TextView) findViewById(R.id.momname);
            momname.setText(mom.getString("name"));
            momphone = (TextView) findViewById(R.id.momphone);
            momphone.setText(mom.getString("phone"));
        } catch (Exception e){
            Log.d("InputStream", e.getLocalizedMessage());
        }

        findViewById(R.id.logoutButton).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                new AlertDialog.Builder(ParentStudentActivity.this)
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
                    Intent goToMainActivity = new Intent(getApplicationContext(), LoginActivity.class);
                    startActivity(goToMainActivity);
                    ss.logout();
                } else {
                    Toast.makeText(getBaseContext(), callbackJson.getString("message"), Toast.LENGTH_LONG).show();
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
