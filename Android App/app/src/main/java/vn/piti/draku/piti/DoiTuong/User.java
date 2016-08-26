package vn.piti.draku.piti.DoiTuong;

import android.util.Log;
import org.json.JSONObject;

/**
 * Created by Draku on 8/14/2016.
 */
public class User{
    private String username, password;

    public void set(String a, String b) {
        this.username = a;
        this.password = b;
    }

    public String toJson(){
        try {
            JSONObject jsonObject = new JSONObject();
            jsonObject.accumulate("username", this.username);
            jsonObject.accumulate("password", this.password);
            return jsonObject.toString();
        } catch (Exception e) {
            Log.d("InputStream", e.getLocalizedMessage());
        }
        return "Do not work!";
    }
}

