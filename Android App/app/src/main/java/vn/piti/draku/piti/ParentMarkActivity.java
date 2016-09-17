package vn.piti.draku.piti;

import android.content.Intent;
import android.content.res.Configuration;
import android.os.Build;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentStatePagerAdapter;
import android.support.v4.view.ViewPager;
import android.support.v7.app.ActionBarActivity;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.ImageView;

import org.json.JSONArray;
import org.json.JSONObject;

import it.neokree.materialtabs.MaterialTab;
import it.neokree.materialtabs.MaterialTabHost;
import it.neokree.materialtabs.MaterialTabListener;

public class ParentMarkActivity extends ActionBarActivity implements MaterialTabListener{MaterialTabHost tabHost;
    ViewPager pager;
    ViewPagerAdapter adapter;
    ImageView back;
    ParseInfo info;
    JSONArray mark;
    JSONObject subject;
    int manHinh;
    AppConfig config;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_mark_parent);
        changeColor(getWindow(), getResources().getColor(R.color.material_red_400));

        findViewById(R.id.backButton).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToMainActivity = new Intent(getApplicationContext(), MainParentActivity.class);
                startActivity(goToMainActivity);
            }
        });

        info = new ParseInfo(getBaseContext());
        try{
            mark = info.getMark();
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
                subject = mark.getJSONObject(num);
                return FragmentMark.newInstance(subject.getString("hs1"), subject.getString("hs2"), subject.getString("hs3"), getManHinh());
            }
            catch(Exception e){
                Log.d("InputStream", e.getLocalizedMessage());
            }
            return new FragmentMark();
        }

        @Override
        public int getCount() {
            return mark.length();
        }

        @Override
        public CharSequence getPageTitle(int position) {
            try{
                subject = mark.getJSONObject(position);
                return subject.getString("name");
            }
            catch(Exception e){
                Log.d("InputStream", e.getLocalizedMessage());
            }
            return "Môn " + position;
        }

    }

    public int getManHinh(){
        return manHinh;
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
    }

    public void setPortrait(){
        manHinh = Configuration.ORIENTATION_PORTRAIT;

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
    }
    public void changeColor(Window window, int color){
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
            window.setStatusBarColor(color);
        }
    }
}
