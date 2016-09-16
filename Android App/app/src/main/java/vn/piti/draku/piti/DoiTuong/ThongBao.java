package vn.piti.draku.piti.DoiTuong;

import android.content.Context;
import android.util.Log;
import org.json.JSONObject;

import vn.piti.draku.piti.SessionManager;

public class ThongBao{
    private String student, content, token;
    SessionManager ss;

    public ThongBao(Context ct){
        ss = new SessionManager(ct);
        this.token = ss.getToken();
        this.student = new String("");
        this.content = new String("");
    }
    public void setStudent(String st){
        this.student = st;
    }

    public void setContent(String ct){
        this.content = ct;
    }

    public String toJson(){
        try {
            JSONObject jsonObject = new JSONObject();
            JSONObject data = new JSONObject();
            data.accumulate("content", this.content);
            data.accumulate("student", this.student);
            jsonObject.accumulate("token", this.token);
            jsonObject.accumulate("object", "notification");
            jsonObject.accumulate("data", data);
            return jsonObject.toString();
        } catch (Exception e) {
            Log.d("InputStream", e.getLocalizedMessage());
        }
        return "Do not work!";
    }

    public String getStudent(){
        return this.student;
    }

    public boolean invalid(){
        return (this.student.equals("") || this.content.equals(""));
    }
}
