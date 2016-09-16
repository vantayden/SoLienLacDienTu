package vn.piti.draku.piti;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.view.Window;


public class SplashScreensActivity extends Activity {
    SessionManager ss;
    @Override
    public void onBackPressed(){
        //Do nothing
    }
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        ss = new SessionManager(getBaseContext());
        super.onCreate(savedInstanceState);
        getWindow().requestFeature(Window.FEATURE_NO_TITLE);
        setContentView(R.layout.activity_splash_screen);
        new Handler().postDelayed(new Runnable() {
            public void run() {
                Intent goToNextActivity;
                if(!ss.isLoggedIn()){
                    goToNextActivity = new Intent(getApplicationContext(), LoginActivity.class);
                } else if(ss.getType() == 1){
                    goToNextActivity = new Intent(getApplicationContext(), MainTeacherActivity.class);
                } else {
                    goToNextActivity = new Intent(getApplicationContext(), MainParentActivity.class);
                }
                startActivity(goToNextActivity);
            }
        }, 2000);
    }
}