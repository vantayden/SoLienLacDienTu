package vn.piti.draku.piti;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.ProgressDialog;
import android.content.Intent;
import android.graphics.Color;
import android.os.AsyncTask;
import android.os.Bundle;
import android.text.InputType;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.Toast;

import com.aurelhubert.ahbottomnavigation.AHBottomNavigation;
import com.aurelhubert.ahbottomnavigation.AHBottomNavigationItem;

import org.json.JSONObject;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Locale;

import vn.piti.draku.piti.DoiTuong.Ask;

public class ParentAskActivity extends Activity implements View.OnClickListener {
    AppConfig config;
    private EditText askReason;
    private EditText date;
    private ImageView back;
    private Button btnSend;
    private ProgressDialog progressDialog;
    private DatePickerDialog datePickerDialog;
    private SimpleDateFormat dateFormatter;
    private Ask ask;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_ask_parent);
        createNavigation(4);
        dateFormatter = new SimpleDateFormat("dd-MM-yyyy", Locale.US);

        findViewsById();
        setDateTimeField();
    }

    private void findViewsById() {
        askReason = (EditText) findViewById(R.id.askReason);
        date = (EditText) findViewById(R.id.date);
        date .setInputType(InputType.TYPE_NULL);
        date .requestFocus();
        back = (ImageView) findViewById(R.id.backButton);
        btnSend = (Button) findViewById(R.id.btnSend);
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

        back.setOnClickListener(new View.OnClickListener(){
            @Override
            public void onClick(View v) {
                Intent goToMainActivity = new Intent(getApplicationContext(), MainParentActivity.class);
                startActivity(goToMainActivity);
            }
        });

        btnSend.setOnClickListener(new View.OnClickListener(){
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
                    new HttpAsyncTask().execute(config.ADD_ASK_URL);
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
                int code = callbackJson.getInt("code");
                if(code == 0){
                    Toast.makeText(getBaseContext(), callbackJson.getString("message"), Toast.LENGTH_LONG).show();
                } else {
                    Toast.makeText(getBaseContext(), callbackJson.getString("message"), Toast.LENGTH_LONG).show();
                    Intent goToNextActivity = new Intent(getApplicationContext(), MainParentActivity.class);
                    startActivity(goToNextActivity);
                }
            } catch (Exception e) {
                Log.d("InputStream", e.getLocalizedMessage());
            }
        }
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

}
