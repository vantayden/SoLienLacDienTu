package vn.piti.draku.piti;


import android.content.Context;
import android.util.Log;

import org.json.JSONArray;
import org.json.JSONObject;


public class ParseInfo {
    SessionManager ss;
    JSONObject student, JSONinfo, dad, mom, teacher;
    JSONArray mark, schedule, myClass, attendanceClass;

    public ParseInfo(Context ct){
        this.ss = new SessionManager(ct);
        String info = ss.getInfo();
        try {
            this.JSONinfo = new JSONObject(info);
            this.schedule = JSONinfo.getJSONArray("schedule");
            if(ss.getType() == 2) {
                this.student = JSONinfo.getJSONObject("student");
                this.dad = JSONinfo.getJSONObject("dad");
                this.mom = JSONinfo.getJSONObject("mom");
                this.mark = JSONinfo.getJSONArray("mark");
            } else if(ss.getType() == 1){
                this.teacher = JSONinfo.getJSONObject("teacher");
                this.myClass = new JSONArray(ss.getMyClass());
                this.attendanceClass = new JSONArray(ss.getAttendanceClass());
            }
        }  catch (Exception e) {
            Log.d("InputStream", e.getLocalizedMessage());
        }
    }

    public String getStudent(){
        try {
            return student.getString("id");
        }  catch (Exception e) {
            Log.d("InputStream", e.getLocalizedMessage());
        }
        return "failed";
    }

    public JSONObject getTeacher(){
        try {
            return teacher;
        }  catch (Exception e) {
            Log.d("InputStream", e.getLocalizedMessage());
        }
        return null;
    }

    public JSONObject getStudentInfo(){
        try {
            return student;
        }  catch (Exception e) {
            Log.d("InputStream", e.getLocalizedMessage());
        }
        return null;
    }

    public JSONObject getDadInfo(){
        try {
            return dad;
        }  catch (Exception e) {
            Log.d("InputStream", e.getLocalizedMessage());
        }
        return null;
    }

    public JSONObject getMomInfo(){
        try {
            return mom;
        }  catch (Exception e) {
            Log.d("InputStream", e.getLocalizedMessage());
        }
        return null;
    }
    public JSONArray getMark(){
        try {
            return mark;
        }  catch (Exception e) {
            Log.d("InputStream", e.getLocalizedMessage());
        }
        return null;
    }
    public JSONArray getSchedule(){
        try {
            return schedule;
        }  catch (Exception e) {
            Log.d("InputStream", e.getLocalizedMessage());
        }
        return null;
    }
    public JSONArray getMyClass(){
        try {
            return myClass;
        }  catch (Exception e) {
            Log.d("InputStream", e.getLocalizedMessage());
        }
        return null;
    }

    public JSONArray getTeacherClass(){
        try {
            return JSONinfo.getJSONArray("myClass");
        }  catch (Exception e) {
            Log.d("InputStream", e.getLocalizedMessage());
        }
        return null;
    }

    public JSONArray getAttendanceClass() {
        try {
            return attendanceClass;
        }  catch (Exception e) {
            Log.d("InputStream", e.getLocalizedMessage());
        }
        return null;
    }
}

