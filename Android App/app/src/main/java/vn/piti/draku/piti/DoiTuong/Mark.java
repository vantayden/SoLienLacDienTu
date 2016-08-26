package vn.piti.draku.piti.DoiTuong;

import android.content.Context;
import android.util.Log;

import org.json.JSONObject;

import vn.piti.draku.piti.SessionManager;

public class Mark {
    private String student, mark, type, token;
    SessionManager ss;

    public Mark(Context ct){
        ss = new SessionManager(ct);
        this.token = ss.getToken();
        this.student = new String("");
        this.mark = new String("");
    }
    public void setStudent(String st){
        this.student = st;
    }

    public void setMark(String mark){
        this.mark = mark;
    }
    public void setType(String type){
        this.type = type;
    }

    public String toJson(){
        try {
            JSONObject jsonObject = new JSONObject();
            jsonObject.accumulate("mark", this.mark);
            jsonObject.accumulate("student", this.student);
            jsonObject.accumulate("type", this.type);
            jsonObject.accumulate("token", this.token);
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
        return (this.student.equals("") || this.mark.equals("") || this.type.equals(""));
    }
}