package vn.piti.draku.piti.Objects;

import android.content.Context;
import android.util.Log;
import org.json.JSONObject;

import vn.piti.draku.piti.Tools.SessionManager;
import vn.piti.draku.piti.Tools.ParseInfo;

public class Ask{
    private String content, date, token;
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
            jsonObject.accumulate("token", this.token);
            jsonObject.accumulate("object", "ask");

            JSONObject data = new JSONObject();
            data.accumulate("date", this.date);
            data.accumulate("content", this.content);
            jsonObject.accumulate("data", data);

            return jsonObject.toString();
        } catch (Exception e) {
            Log.d("InputStream", e.getLocalizedMessage());
        }
        return "Do not work!";
    }
}

