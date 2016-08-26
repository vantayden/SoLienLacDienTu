package vn.piti.draku.piti;

import android.content.Context;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;
import android.util.Log;

public class SessionManager {
    // LogCat tag
    private static String TAG = SessionManager.class.getSimpleName();

    // Shared Preferences
    SharedPreferences pref;

    Editor editor;
    Context _context;

    // Shared pref mode
    int PRIVATE_MODE = 0;

    // Shared preferences file name
    private static final String PREF_NAME = "ParteaLogin";
    private static final String KEY_IS_LOGGEDIN = "isLoggedIn";
    private static final String TOKEN_STRING = "token";
    private static final String USER_TYPE = "type";
    private static final String INFO = "info";
    private static final String INFO_UPDATED = "info_update";
    private static final String MY_CLASS = "my_class";
    private static final String ATTENDANCE_CLASS = "attendance_class";

    public SessionManager(Context context) {
        this._context = context;
        pref = _context.getSharedPreferences(PREF_NAME, PRIVATE_MODE);
        editor = pref.edit();
        editor.apply();
    }

    public void setLogin(boolean isLoggedIn) {

        editor.putBoolean(KEY_IS_LOGGEDIN, isLoggedIn);

        // commit changes
        editor.commit();

        Log.d(TAG, "User login session modified!");
    }

    public void setToken(String token) {

        editor.putString(TOKEN_STRING, token);

        // commit changes
        editor.commit();

        Log.d(TAG, "Token updated");
    }

    public void setInfo(String result) {
        editor.putString(INFO, result);
        editor.putBoolean(INFO_UPDATED, true);
        editor.commit();
    }

    public void setType(int type) {
        editor.putInt(USER_TYPE, type);
        editor.commit();
    }

    public void setMyClass(String result) {
        editor.putString(MY_CLASS, result);
        editor.commit();
    }
    public void setAttendanceClass(String result) {
        editor.putString(ATTENDANCE_CLASS, result);
        editor.commit();
    }

    public boolean isLoggedIn(){
        return pref.getBoolean(KEY_IS_LOGGEDIN, false);
    }
    public boolean infoUpdated(){
        return pref.getBoolean(INFO_UPDATED, false);
    }
    public String getToken() { return pref.getString(TOKEN_STRING, "Default"); }
    public String getInfo() { return pref.getString(INFO, "Default");}
    public int getType() { return pref.getInt(USER_TYPE, 0);}
    public String getMyClass() { return pref.getString(MY_CLASS, "default");}
    public String getAttendanceClass() { return pref.getString(ATTENDANCE_CLASS, "default");}

    public void logout(){
        editor.clear();
        editor.commit();
    }
}

