package vn.piti.draku.piti;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.ProgressDialog;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.text.InputType;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.Toast;

import org.json.JSONObject;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Locale;

import vn.piti.draku.piti.DoiTuong.Ask;

public class ParentAskActivity extends Activity implements View.OnClickListener {
    AppConfig config;
    private EditText askReason;
    private EditText date;
    private ProgressDialog progressDialog;
    private DatePickerDialog datePickerDialog;
    private SimpleDateFormat dateFormatter;
    private Ask ask;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_ask_parent);
        changeColor(getWindow(), getResources().getColor(R.color.material_brown_400));
        dateFormatter = new SimpleDateFormat("dd-MM-yyyy", Locale.US);

        findViewsById();
        setDateTimeField();
    }

    private void findViewsById() {
        askReason = (EditText) findViewById(R.id.askReason);
        date = (EditText) findViewById(R.id.date);
        date .setInputType(InputType.TYPE_NULL);
        date .requestFocus();
    }
    private void setDateTimeField() {
        date.setOnClickListener(this);

        Calendar newCalendar = Calendar.getInstance();
        datePickerDialog = new DatePickerDialog(this, new DatePickerDialog.OnDateSetListener() {

            public void onDateSet(DatePicker view, int year, int monthOfYear, int dayOfMonth) {
                Calendar newDate = Calendar.getInstance();
                newDate.set(year, monthOfYear, dayOfMonth);
                date.setText(dateFormatter.format(newDate.getTime()));
            }

        },newCalendar.get(Calendar.YEAR), newCalendar.get(Calendar.MONTH), newCalendar.get(Calendar.DAY_OF_MONTH));

        findViewById(R.id.backButton).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToMainActivity = new Intent(getApplicationContext(), MainParentActivity.class);
                startActivity(goToMainActivity);
            }
        });

        findViewById(R.id.sendButton).setOnClickListener(new View.OnClickListener(){
            @Override
            public void onClick(View v) {
                progressDialog = new ProgressDialog(ParentAskActivity.this);
                if(!validate())
                    Toast.makeText(getBaseContext(), "Vui lòng nhập dữ liệu!", Toast.LENGTH_LONG).show();
                    // call AsynTask to perform network operation on separate thread
                else {
                    progressDialog.setIndeterminate(true);
                    progressDialog.setMessage("Đang gửi");
                    progressDialog.show();
                    ask = new Ask();
                    ask.set(askReason.getText().toString(), date.getText().toString(), getBaseContext());
                    new HttpAsyncTask().execute(config.ADD_URL);
                }
            }
        });
    }

    @Override
    public void onClick(View view) {
        if(view == date) {
            datePickerDialog.show();
        }
    }

    private boolean validate(){
        return (!askReason.getText().toString().trim().equals("") && !date.getText().toString().trim().equals(""));
    }

    private class HttpAsyncTask extends AsyncTask<String, Void, String> {
        private DoPost p = new DoPost();
        @Override
        protected String doInBackground(String... urls) {
            return p.POST(urls[0], ask.toJson());
        }
        // onPostExecute displays the results of the AsyncTask.
        @Override
        protected void onPostExecute(String result) {
            progressDialog.dismiss();
            try {
                JSONObject callbackJson = new JSONObject(result);
                boolean status = callbackJson.getBoolean("status");
                if(status == false){
                    Toast.makeText(getBaseContext(), callbackJson.getString("message"), Toast.LENGTH_LONG).show();
                } else {
                    Toast.makeText(getBaseContext(), callbackJson.getString("message"), Toast.LENGTH_LONG).show();
                    Intent goToNextActivity = new Intent(getApplicationContext(), MainParentActivity3.class);
                    startActivity(goToNextActivity);
                }
            } catch (Exception e) {
                Log.d("InputStream", e.getLocalizedMessage());
            }
        }
    }
    public void changeColor(Window window, int color){
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
            window.setStatusBarColor(color);
        }
    }
}
