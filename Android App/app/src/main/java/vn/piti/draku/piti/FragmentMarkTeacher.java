package vn.piti.draku.piti;

import android.content.DialogInterface;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v7.app.AlertDialog;
import android.text.InputType;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;

import pl.coreorb.selectiondialogs.data.SelectableColor;
import pl.coreorb.selectiondialogs.data.SelectableIcon;
import pl.coreorb.selectiondialogs.dialogs.ColorSelectDialog;
import pl.coreorb.selectiondialogs.dialogs.IconSelectDialog;
import pl.coreorb.selectiondialogs.views.SelectedItemView;
import vn.piti.draku.piti.DoiTuong.Mark;

public class FragmentMarkTeacher extends Fragment implements IconSelectDialog.OnIconSelectedListener,
        ColorSelectDialog.OnColorSelectedListener {

    private static final String TAG_SELECT_ICON_DIALOG = "TAG_SELECT_ICON_DIALOG";
    private static final String TAG_SELECT_COLOR_DIALOG = "TAG_SELECT_COLOR_DIALOG";
    private static final String TAG_SELECT_TEXT_DIALOG = "TAG_SELECT_TEXT_DIALOG";

    private SelectedItemView classSIV;
    private SelectedItemView studentSIV;
    private SelectedItemView typeSIV;
    private SelectedItemView markSIV;
    Button btnSend;
    Mark mark;
    AppConfig config;
    private JSONArray myClass;

    public FragmentMarkTeacher() {
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        mark = new Mark(getContext());

        View rootView = inflater.inflate(R.layout.fragment_mark, container, false);

        classSIV = (SelectedItemView) rootView.findViewById(R.id.class_siv);
        classSIV.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                showIconSelectDialog();
            }
        });

        studentSIV = (SelectedItemView) rootView.findViewById(R.id.student_siv);
        studentSIV.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if(classSIV.getSelectedIcon().getId().equals("no_id"))
                    Toast.makeText(getContext(), "Chọn lớp học trước!", Toast.LENGTH_LONG).show();
                else
                    showColorSelectDialog();
            }
        });

        final AlertDialog.Builder adb = new AlertDialog.Builder(getContext());
        final CharSequence items[] = new CharSequence[] {"Hệ số 1", "Hệ số 2", "Hệ số 3"};
        adb.setSingleChoiceItems(items, 0, new DialogInterface.OnClickListener() {

            @Override
            public void onClick(DialogInterface d, int n) {
                typeSIV.setSelectedName(items[n].toString());
                mark.setType(Integer.toString(n+1));
            }

        });
        adb.setNegativeButton("Đóng", null);
        adb.setTitle("Chọn hệ số...");
        typeSIV = (SelectedItemView) rootView.findViewById(R.id.type_siv);
        typeSIV.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                adb.show();
            }
        });

        markSIV = (SelectedItemView) rootView.findViewById(R.id.mark_siv);
        markSIV.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                showTextInputDialog();
            }
        });

        btnSend = (Button) rootView.findViewById(R.id.btnSend);
        btnSend.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if(mark.invalid())
                    Toast.makeText(getContext(), "Vui lòng nhập đủ dữ liệu!", Toast.LENGTH_LONG).show();
                else {
                    new HttpAsyncTask().execute(config.TEACHER_ADD_MARK_URL);
                }
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
        ColorSelectDialog colorDialog = (ColorSelectDialog) getFragmentManager().findFragmentByTag(TAG_SELECT_COLOR_DIALOG);
        if (colorDialog != null) {
            colorDialog.setOnColorSelectedListener(this);
        }
    }

    /**
     * Displays selected icon in {@link SelectedItemView} view.
     * @param selectedItem selected {@link SelectableIcon} object containing: id, name and drawable resource id.
     */
    @Override
    public void onIconSelected(SelectableIcon selectedItem) {
        classSIV.setSelectedIcon(selectedItem);
    }

    /**
     * Displays selected color in {@link SelectedItemView} view.
     * @param selectedItem selected {@link SelectableColor} object containing: id, name and color value.
     */
    @Override
    public void onColorSelected(SelectableColor selectedItem) {
        studentSIV.setSelectedColor(selectedItem);
        mark.setStudent(selectedItem.getId());
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
     * Shows color selection dialog with default Material Design icons.
     */
    private void showColorSelectDialog() {

        new ColorSelectDialog.Builder(getContext())
                .setColors(listStudent(Integer.parseInt(classSIV.getSelectedIcon().getId())))
                .setTitle("Chọn học sinh...")
                .setSortColorsByName(true)
                .setOnColorSelectedListener(this)
                .build().show(getFragmentManager(), TAG_SELECT_COLOR_DIALOG);
    }


    private void showTextInputDialog() {
        final EditText textET = new EditText(getContext());
        textET.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL | InputType.TYPE_NUMBER_FLAG_SIGNED);

        new AlertDialog.Builder(getContext())
                .setTitle("Nhập điểm...")
                .setView(textET)
                .setPositiveButton(android.R.string.ok, new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int whichButton) {
                        markSIV.setSelectedName(textET.getText().toString());
                        mark.setMark(textET.getText().toString());
                    }
                })
                .setNegativeButton(android.R.string.cancel, new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int whichButton) {
                    }
                })
                .show();
    }

    /**
     * Creates sample ArrayList of icons to display in dialog.
     * @return sample icons
     */
    private  ArrayList<SelectableColor> listStudent(int id) {
        ArrayList<SelectableColor> selectionDialogsColors = new ArrayList<>();
        try {
            JSONObject single_class, single_student;
            int j=0;
            do{
                single_class = myClass.getJSONObject(j);
                j++;
            } while(j<myClass.length() && single_class.getInt("id")!=id);

            JSONArray student = single_class.getJSONArray("student");
            for(int i=0; i<student.length();i++){
                single_student = student.getJSONObject(i);
                selectionDialogsColors.add(new SelectableColor(single_student.getString("id"), single_student.getString("name"), R.color.main1));
            }
        }  catch (Exception e) {
            Log.d("InputStream", e.getLocalizedMessage());
        }
        return selectionDialogsColors;
    }


    private ArrayList<SelectableIcon> sampleIcons() {
        TeacherMarkActivity myactivi = (TeacherMarkActivity) getActivity();

        myClass = myactivi.getMyClass();
        ArrayList<SelectableIcon> selectionDialogsColors = new ArrayList<>();
        try {
            JSONObject single_class;
            for(int i=0; i<myClass.length();i++){
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
            return p.POST(urls[0], mark.toJson());
        }
        // onPostExecute displays the results of the AsyncTask.
        @Override
        protected void onPostExecute(String result) {
            try {
                JSONObject callbackJson = new JSONObject(result);
                int code = callbackJson.getInt("code");
                if(code == 0){
                    Toast.makeText(getContext(), callbackJson.getString("message"), Toast.LENGTH_LONG).show();
                } else {
                    Toast.makeText(getContext(), callbackJson.getString("message"), Toast.LENGTH_LONG).show();
                    Intent goToNextActivity = new Intent(getContext(), MainTeacherActivity.class);
                    startActivity(goToNextActivity);
                }
            } catch (Exception e) {
                Log.d("InputStream", e.getLocalizedMessage());
            }
        }
    }
}
