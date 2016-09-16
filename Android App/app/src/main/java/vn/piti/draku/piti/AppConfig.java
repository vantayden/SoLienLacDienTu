package vn.piti.draku.piti;

import android.graphics.Color;
import android.os.Build;
import android.view.Window;
import android.view.WindowManager;

public class AppConfig {
    // Server user login url
    public static String CORE_URL = "http://loltus.com/v2/";
    public static String API_URL = CORE_URL + "api/";
    public static String IMAGE_URL = CORE_URL + "image/";
    public static String LOGIN_URL = API_URL + "login";
    public static String GET_STUDENT_INFO = API_URL + "get/student/info";
    public static String ADD_URL = API_URL + "add";
    public static String GET_NOTIFICATION = API_URL + "get/student/notification";

    public static String GET_TEACHER = API_URL + "get/teacher/";
    public static String GET_TEACHER_INFO = API_URL + "get/teacher/info";
    public static String GET_TEACHER_CLASS = API_URL + "get/teacher/class";
    public static String GET_ATTENDANCE_CLASS = API_URL + "get/teacher/attendanceClass";

    public static String FCM_TOKEN = API_URL + "FCMToken";
    public static String LOGOUT_URL = API_URL + "logout";

    public static String DEMO_PARENT_USERNAME = "01234567899";
    public static String DEMO_PARENT_PASSWORD = "123456";
    public static String DEMO_TEACHER_USERNAME = "0987654321";
    public static String DEMO_TEACHER_PASSWORD = "123456";

}
