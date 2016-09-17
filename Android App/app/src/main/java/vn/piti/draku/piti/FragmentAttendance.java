package vn.piti.draku.piti;

import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ListView;
import android.widget.Toast;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;

import pl.coreorb.selectiondialogs.data.SelectableIcon;
import pl.coreorb.selectiondialogs.dialogs.IconSelectDialog;
import pl.coreorb.selectiondialogs.views.SelectedItemView;
import vn.piti.draku.piti.DoiTuong.Attendance;
public class FragmentAttendance extends Fragment implements IconSelectDialog.OnIconSelectedListener {

    private static final String TAG_SELECT_ICON_DIALOG = "TAG_SELECT_ICON_DIALOG";

    private SelectedItemView iconSIV;
    AppConfig config;
    View rootView;
    ListView lv;
    JSONArray myClass;

    ArrayList<Attendance> listStudent = new ArrayList<Attendance>();;


    public FragmentAttendance() {
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        rootView = inflater.inflate(R.layout.fragment_attendance, container, false);

        iconSIV = (SelectedItemView) rootView.findViewById(R.id.class_siv);
        iconSIV.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                showIconSelectDialog();
            }
        });

        rootView.findViewById(R.id.backButton).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent goToMainActivity = new Intent(getContext(), MainTeacherActivity.class);
                startActivity(goToMainActivity);
            }
        });

        rootView.findViewById(R.id.sendButton).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                new HttpAsyncTask().execute(config.ADD_URL);
            }
        });
        return rootView;
    }

    @Override
    public void onResume() {
        super.onResume();
        IconSelectDialog iconDialog = (IconSelectDialog) getFragmentManager().findFragmentByTag(TAG_SELECT_ICON_DIALOG);
        if (iconDialog != null) {
            iconDialog.setOnIconSelectedListener(this);
        }

    }

    /**
     * Displays selected icon in {@link SelectedItemView} view.
     * @param selectedItem selected {@link SelectableIcon} object containing: id, name and drawable resource id.
     */
    @Override
    public void onIconSelected(SelectableIcon selectedItem) {
        iconSIV.setSelectedIcon(selectedItem);
        initListStudent(Integer.parseInt(selectedItem.getId()));
    }


    /**
     * Shows icon selection dialog with sample icons.
     */
    private void showIconSelectDialog() {
        new IconSelectDialog.Builder(getContext())
                .setIcons(sampleIcons())
                .setTitle("Chọn lớp...")
                .setSortIconsByName(true)
                .setOnIconSelectedListener(this)
                .build().show(getFragmentManager(), TAG_SELECT_ICON_DIALOG);
    }

    /**
     * Creates sample ArrayList of icons to display in dialog.
     * @return sample icons
     */

    private ArrayList<SelectableIcon> sampleIcons() {
        TeacherAttendanceActivity myactivi = (TeacherAttendanceActivity) getActivity();

        myClass = myactivi.getAttendanceClass();
        ArrayList<SelectableIcon> selectionDialogsColors = new ArrayList<>();
        try {
            JSONObject single_class;
            for(int i=0; i < myClass.length();i++){
                single_class = myClass.getJSONObject(i);
                selectionDialogsColors.add(new SelectableIcon(single_class.getString("id"), single_class.getString("name"), R.drawable.ic_class));
            }
        }  catch (Exception e) {
            Log.d("InputStream", e.getLocalizedMessage());
        }
        return selectionDialogsColors;
    }

    private class HttpAsyncTask extends AsyncTask<String, Void, String> {
        DoPost p = new DoPost();
        @Override
        protected String doInBackground(String... urls) {
            for(int i = 0; i < listStudent.size(); i++)
                p.POST(urls[0], listStudent.get(i).toJson(getContext()));
            return "Điểm danh thành công!";
        }
        // onPostExecute displays the results of the AsyncTask.
        @Override
        protected void onPostExecute(String result) {
            Toast.makeText(getContext(), result, Toast.LENGTH_LONG).show();
            Intent goToMainActivity = new Intent(getContext(), MainTeacherActivity3.class);
            startActivity(goToMainActivity);
        }
    }

    private void initListStudent(int id){
        try {
            //ListView lv = (ListView) rootView.findViewById(R.id.listView);
            listStudent = new ArrayList<Attendance>();
            Attendance student;
            JSONObject single_class, single_student;
            JSONArray students;
            int j = 0;
            do{
                single_class = myClass.getJSONObject(j);
                j++;
            } while(j < myClass.length() && single_class.getInt("id") != id);
            students = single_class.getJSONArray("student");

            for(int i = 0; i < students.length(); i++){
                single_student = students.getJSONObject(i);
                student = new Attendance();
                student.setStudent(single_student.getString("name"));
                student.setStatus(single_student.getInt("type"));
                if(student.getStatus() == 2)
                    student.setReason(single_student.getString("reason"));
                else
                    student.setReason("Không");
                student.setId(single_student.getString("id"));
                listStudent.add(student);
            }
            lv = (ListView) rootView.findViewById(R.id.listStudent);
            lv.setVisibility(View.VISIBLE);
            lv.setAdapter(new CustomAttendanceListAdapter(getContext(), listStudent));

        }  catch (Exception e) {
            Log.d("InputStream", e.getLocalizedMessage());
        }
    }
}
