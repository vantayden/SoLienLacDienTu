package vn.piti.draku.piti;

public class AppConfig {
    // Server user login url
    public static String API_URL = "http://loltus.com/api/";
    public static String LOGIN_URL = API_URL + "login";
    public static String GET_STUDENT_INFO = API_URL + "student/getInfo";
    public static String ADD_ASK_URL = API_URL + "ask/add";
    public static String GET_NOTIFICATION = API_URL + "notification/get";

    public static String GET_TEACHER_INFO = API_URL + "teacher/getInfo";
    public static String GET_TEACHER_CLASS = API_URL + "teacher/getMyClass";
    public static String GET_ATTENDANCE_CLASS = API_URL + "attendance/getClass";
    public static String TEACHER_ADD_NOTIFICATION_URL = API_URL + "teacher/addNotification";
    public static String TEACHER_ADD_ATTENDANCE_URL = API_URL + "attendance/add";
    public static String TEACHER_ADD_MARK_URL = API_URL + "mark/add";

    public static String DEMO_PARENT_USERNAME = "01234567899";
    public static String DEMO_PARENT_PASSWORD = "123456";
    public static String DEMO_TEACHER_USERNAME = "0987654321";
    public static String DEMO_TEACHER_PASSWORD = "123456";

    public static String FCM_TOKEN = API_URL + "FCMToken";
    public static String LOGOUT_URL = API_URL + "logout";
}
