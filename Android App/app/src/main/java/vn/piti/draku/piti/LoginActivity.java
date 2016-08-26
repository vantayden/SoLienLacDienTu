package vn.piti.draku.piti;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.EditText;
import android.widget.Toast;

import com.google.firebase.iid.FirebaseInstanceId;

import org.json.JSONObject;
import vn.piti.draku.piti.DoiTuong.User;

public class LoginActivity extends Activity {
    ProgressDialog progress;
    User user;
    AppConfig config;
    EditText username;
    EditText password;

    @Override
    public void onBackPressed(){
        //Do nothing
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        progress = new ProgressDialog(this);
        progress.setMessage("Đang đăng nhập...");
        progress.setProgressStyle(ProgressDialog.STYLE_SPINNER);

        findViewById(R.id.btn_login).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                user = new User();
                username = (EditText) findViewById(R.id.username);
                password = (EditText) findViewById(R.id.password);
                if(!validate())
                    Toast.makeText(getBaseContext(), "Vui lòng nhập đủ dữ liệu!", Toast.LENGTH_LONG).show();
                else {
                    if(!isConnected())
                        Toast.makeText(getBaseContext(), "Không có kết nối mạng!", Toast.LENGTH_LONG).show();
                    else {
                        progress.show();
                        user.set(username.getText().toString(), password.getText().toString());

                        new HttpAsyncTask().execute(config.LOGIN_URL);
                    }
                }
            }
        });

        findViewById(R.id.btn_login_parent_sample).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                user = new User();
                if(!isConnected())
                    Toast.makeText(getBaseContext(), "Không có kết nối mạng!", Toast.LENGTH_LONG).show();
                else {
                    progress.show();
                    user.set(config.DEMO_PARENT_USERNAME, config.DEMO_PARENT_PASSWORD);

                    new HttpAsyncTask().execute(config.LOGIN_URL);
                }
            }
        });

        findViewById(R.id.btn_login_teacher_sample).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                user = new User();
                if(!isConnected())
                    Toast.makeText(getBaseContext(), "Không có kết nối mạng!", Toast.LENGTH_LONG).show();
                else {
                    progress.show();
                    user.set(config.DEMO_TEACHER_USERNAME, config.DEMO_TEACHER_PASSWORD);

                    new HttpAsyncTask().execute(config.LOGIN_URL);
                }
            }
        });
    }

    private class HttpAsyncTask extends AsyncTask<String, Void, String> {
        DoPost p = new DoPost();
        @Override
        protected String doInBackground(String... urls) {
            return p.POST(urls[0], user.toJson());
        }
        // onPostExecute displays the results of the AsyncTask.
        @Override
        protected void onPostExecute(String result) {
            progress.dismiss();
            try {
                JSONObject callbackJson = new JSONObject(result);
                int code = callbackJson.getInt("code");
                if(code == 0){
                    Toast.makeText(getBaseContext(), callbackJson.getString("message"), Toast.LENGTH_LONG).show();
                } else {
                    SessionManager ss = new SessionManager(getBaseContext());
                    ss.setLogin(true);
                    ss.setToken(callbackJson.getString("token"));
                    ss.setType(callbackJson.getInt("type"));
                    if(callbackJson.getInt("type") == 2){
                        Intent goToNextActivity = new Intent(getApplicationContext(), MainParentActivity.class);
                        startActivity(goToNextActivity);
                    } else if(callbackJson.getInt("type") == 1){
                        Intent goToNextActivity = new Intent(getApplicationContext(), MainTeacherActivity.class);
                        startActivity(goToNextActivity);
                    }


                }
            } catch (Exception e) {
                Log.d("InputStream", e.getLocalizedMessage());
            }
        }
    }

    private boolean validate(){
        return (!username.getText().toString().trim().equals("") && !password.getText().toString().trim().equals(""));
    }
    public boolean isConnected(){
        ConnectivityManager connMgr = (ConnectivityManager) getSystemService(Activity.CONNECTIVITY_SERVICE);
        NetworkInfo networkInfo = connMgr.getActiveNetworkInfo();
        return (networkInfo != null && networkInfo.isConnected());
    }

}

