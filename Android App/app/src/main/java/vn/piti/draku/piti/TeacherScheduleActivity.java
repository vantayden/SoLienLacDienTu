package vn.piti.draku.piti;

import android.content.Intent;
import android.content.res.Configuration;
import android.graphics.Color;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentStatePagerAdapter;
import android.support.v4.view.ViewPager;
import android.support.v7.app.ActionBarActivity;
import android.support.v7.widget.Toolbar;
import android.util.Log;
import android.view.View;
import android.widget.ImageView;

import com.aurelhubert.ahbottomnavigation.AHBottomNavigation;
import com.aurelhubert.ahbottomnavigation.AHBottomNavigationItem;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.Calendar;

import it.neokree.materialtabs.MaterialTab;
import it.neokree.materialtabs.MaterialTabHost;
import it.neokree.materialtabs.MaterialTabListener;

public class TeacherScheduleActivity extends ActionBarActivity implements MaterialTabListener{MaterialTabHost tabHost;
    ViewPager pager;
    ViewPagerAdapter adapter;
    ImageView back;
    ParseInfo info;
    JSONArray schedule;
    JSONObject day;
    int manHinh;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_schedule_parent);
        createNavigation(3);

        back = (ImageView) findViewById(R.id.backButton);
        back.setOnClickListener(new View.OnClickListener(){
            @Override
            public void onClick(View v) {
                Intent goToMainActivity = new Intent(getApplicationContext(), MainTeacherActivity.class);
                startActivity(goToMainActivity);
            }
        });

        info = new ParseInfo(getBaseContext());
        try{
            schedule = info.getSchedule();
        }
        catch (Exception e){
            Log.d("InputStream", e.getLocalizedMessage());
        }

        if(this.getResources().getConfiguration().orientation == Configuration.ORIENTATION_LANDSCAPE)
            setLanscape();
        else
            setPortrait();

    }

    @Override
    public void onTabSelected(MaterialTab tab) {
        pager.setCurrentItem(tab.getPosition());
    }

    @Override
    public void onTabReselected(MaterialTab tab) {

    }

    @Override
    public void onTabUnselected(MaterialTab tab) {

    }

    private class ViewPagerAdapter extends FragmentStatePagerAdapter {

        public ViewPagerAdapter(FragmentManager fm) {
            super(fm);

        }

        public Fragment getItem(int num) {
            try{
                day = schedule.getJSONObject(num);
                return FragmentSchedule.newInstance(day.getString("sang"), day.getString("chieu"), day.getString("note"), getManHinh());
            }
            catch(Exception e){
                Log.d("InputStream", e.getLocalizedMessage());
            }
            return new FragmentSchedule();
        }

        @Override
        public int getCount() {
            return schedule.length();
        }

        @Override
        public CharSequence getPageTitle(int position) {
            try{
                day = schedule.getJSONObject(position);
                return day.getString("name");
            }
            catch(Exception e){
                Log.d("InputStream", e.getLocalizedMessage());
            }
            return "Thứ " + (position+2);
        }

    }

    public int getManHinh(){
        return manHinh;
    }

    public void createNavigation(int i){
        AHBottomNavigation bottomNavigation = (AHBottomNavigation) findViewById(R.id.bottom_navigation);

        // Create items
        AHBottomNavigationItem item1 = new AHBottomNavigationItem(R.string.teacher_main_notification, R.drawable.ic_notification, R.color.main1);
        AHBottomNavigationItem item2 = new AHBottomNavigationItem(R.string.teacher_main_profile, R.drawable.ic_student, R.color.main2);
        AHBottomNavigationItem item3 = new AHBottomNavigationItem(R.string.teacher_main_mark, R.drawable.ic_mark, R.color.main3);
        AHBottomNavigationItem item4 = new AHBottomNavigationItem(R.string.teacher_main_schedule, R.drawable.ic_schedule, R.color.main4);
        AHBottomNavigationItem item5 = new AHBottomNavigationItem(R.string.teacher_main_attendance, R.drawable.ic_ask, R.color.main5);

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
                            goToNextActivity = new Intent(getApplicationContext(), TeacherNotificationActivity.class);
                            startActivity(goToNextActivity);
                            break;
                        case 1:
                            goToNextActivity = new Intent(getApplicationContext(), TeacherProfileActivity.class);
                            startActivity(goToNextActivity);
                            break;
                        case 2:
                            goToNextActivity = new Intent(getApplicationContext(), TeacherMarkActivity.class);
                            startActivity(goToNextActivity);
                            break;
                        case 3:
                            goToNextActivity = new Intent(getApplicationContext(), TeacherScheduleActivity.class);
                            startActivity(goToNextActivity);
                            break;
                        case 4:
                            goToNextActivity = new Intent(getApplicationContext(), TeacherAttendanceActivity.class);
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
    public void onConfigurationChanged (Configuration newConfig){
        int orientation = newConfig.orientation;

        switch(orientation) {

            case Configuration.ORIENTATION_LANDSCAPE:
                setLanscape();
                break;

            case Configuration.ORIENTATION_PORTRAIT:
                setPortrait();
                break;

        }
    }
    public void setLanscape(){
        manHinh = Configuration.ORIENTATION_LANDSCAPE;
        initTab();
    }

    public void setPortrait(){
        manHinh = Configuration.ORIENTATION_PORTRAIT;
        initTab();
    }

    public void initTab(){
        Toolbar toolbar = (android.support.v7.widget.Toolbar) this.findViewById(R.id.toolbar);
        this.setSupportActionBar(toolbar);

        tabHost = (MaterialTabHost) this.findViewById(R.id.tabHost);
        pager = (ViewPager) this.findViewById(R.id.pager);

        // init view pager
        adapter = new ViewPagerAdapter(getSupportFragmentManager());
        pager.setAdapter(adapter);
        pager.setOnPageChangeListener(new ViewPager.SimpleOnPageChangeListener() {
            @Override
            public void onPageSelected(int position) {
                // when user do a swipe the selected tab change
                tabHost.setSelectedNavigationItem(position);

            }
        });

        // insert all tabs from pagerAdapter data
        for (int i = 0; i < adapter.getCount(); i++) {
            tabHost.addTab(
                    tabHost.newTab()
                            .setText(adapter.getPageTitle(i))
                            .setTabListener(this)
            );

        }
        switch (Calendar.getInstance().get(Calendar.DAY_OF_WEEK)){
            case 8:
                pager.setCurrentItem(0);
                break;
            default:
                pager.setCurrentItem(Calendar.getInstance().get(Calendar.DAY_OF_WEEK) - 2);
                break;
        }
    }
}
