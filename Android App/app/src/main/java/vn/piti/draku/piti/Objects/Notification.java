package vn.piti.draku.piti.Objects;

import android.util.Log;

import org.json.JSONObject;

public class Notification {
        private JSONObject teacher;
        private String content;
        private String date;

        public JSONObject getTeacher() {
            return teacher;
        }

        public void setTeacher(JSONObject teacher) {
            this.teacher = teacher;
        }

        public String getContent() {
            return content;
        }

        public void setContent(String content) {
            this.content = content;
        }

        public String getDate() {
            return date;
        }

        public void setDate(String date) {
            this.date = date;
        }

        public String getTeacherName(){
            try{
                return this.teacher.getString("name");
            } catch (Exception e) {
                Log.d("InputStream", e.getLocalizedMessage());
            }
            return "";
        }

        public String getTeacherImage(){
            try{
                return this.teacher.getString("image");
            } catch (Exception e) {
                Log.d("InputStream", e.getLocalizedMessage());
            }
            return "";
        }
        public String[] getTeacherInfo(){
            String[] teacherInfo = new String[6];

            try{
                teacherInfo[0] = this.teacher.getString("name");
                teacherInfo[1] = this.teacher.getString("image");
                teacherInfo[2] = this.teacher.getString("type");
                teacherInfo[3] = this.teacher.getString("subject");
                teacherInfo[4] = this.teacher.getString("address");
                teacherInfo[5] = this.teacher.getString("phone");
                return teacherInfo;
            } catch (Exception e) {
                Log.d("InputStream", e.getLocalizedMessage());
            }

            return new String[6];
        }
}
