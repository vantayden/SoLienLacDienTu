package vn.piti.draku.piti.DoiTuong;

//Status
// {0} - Nghi hoc
// {1} - Di hoc
// {2} - Co phep

import android.content.Context;
import android.util.Log;

import org.json.JSONObject;

import vn.piti.draku.piti.SessionManager;

public class Attendance {
    private int status;
    private String student;
    private String reason;
    private String id;
    SessionManager ss;

    public void setReason(String reason) {
        this.reason = reason;
    }
    public void setStatus(int status) {
        this.status = status;
    }
    public void setStudent(String student) {
        this.student = student;
    }
    public void setId(String id) {
        this.id = id;
    }

    public String getStudent() {
        return student;
    }
    public String getReason() {
        return reason;
    }
    public int getStatus() {
        return status;
    }
    public String getId() { return id;}

    public String toJson(Context ct){
        try {
            ss = new SessionManager(ct);
            JSONObject jsonObject = new JSONObject();
            jsonObject.accumulate("student", this.id);
            jsonObject.accumulate("status", this.status);
            jsonObject.accumulate("token", ss.getToken());
            return jsonObject.toString();
        } catch (Exception e) {
            Log.d("InputStream", e.getLocalizedMessage());
        }
        return "Do not work!";
    }
}
