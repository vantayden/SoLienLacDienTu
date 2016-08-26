package vn.piti.draku.piti.DoiTuong;

import android.content.Context;
import android.util.Log;
import org.json.JSONObject;

import vn.piti.draku.piti.SessionManager;
import vn.piti.draku.piti.ParseInfo;

public class Ask{
    private String student, content, date, token;
    SessionManager ss;
    ParseInfo info;

    public void set(String b, String c, Context ct){
        ss = new SessionManager(ct);
        info = new ParseInfo(ct);
        this.content = b;
        this.date = c;
        this.token = ss.getToken();
    }

    public String toJson(){
        try {
            JSONObject jsonObject = new JSONObject();
            jsonObject.accumulate("content", this.content);
            jsonObject.accumulate("date", this.date);
            jsonObject.accumulate("token", this.token);
            return jsonObject.toString();
        } catch (Exception e) {
            Log.d("InputStream", e.getLocalizedMessage());
        }
        return "Do not work!";
    }
}

