package vn.piti.draku.piti.Teacher;

import android.app.DialogFragment;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import com.squareup.picasso.Picasso;

import vn.piti.draku.piti.AppConfig;
import vn.piti.draku.piti.R;


public class FragmentTeacherInfo  extends DialogFragment {

    Context ct;
    AppConfig config;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        View rootView = inflater.inflate(R.layout.teacher_info_fragment, container, false);
        Bundle teacherInfo = getArguments();
        final String[] teacherArray = teacherInfo.getStringArray("teacher_info");

        ImageView teacher_image = (ImageView) rootView.findViewById(R.id.teacher_image);
        if(!teacherArray[1].equals(""))
            Picasso.with(ct).load(config.IMAGE_URL + teacherArray[1]).into(teacher_image);

        TextView teacher_name = (TextView) rootView.findViewById(R.id.teacher_name);
        teacher_name.setText(teacherArray[0]);

        TextView teacher_type = (TextView) rootView.findViewById(R.id.teacher_type);
        teacher_type.setText(teacherArray[2]);

        TextView teacher_subject = (TextView) rootView.findViewById(R.id.teacher_subject);
        teacher_subject.setText(teacherArray[3]);

        TextView teacher_address = (TextView) rootView.findViewById(R.id.teacher_address);
        teacher_address.setText(teacherArray[4]);

        TextView teacher_phone = (TextView) rootView.findViewById(R.id.teacher_phone);
        teacher_phone.setText(teacherArray[5]);

        getDialog().setTitle("Thông tin giáo viên");
        ImageView dismiss = (ImageView) rootView.findViewById(R.id.dismiss);
        dismiss.setVisibility(View.VISIBLE);
        dismiss.setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View v) {
                dismiss();
            }
        });

        rootView.findViewById(R.id.teacher_call).setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View v) {
                Intent intent = new Intent(Intent.ACTION_CALL, Uri.parse("tel:" + teacherArray[5]));
                startActivity(intent);
            }
        });
        return rootView;
    }

    @Override
    public void onAttach(Context context) {
        this.ct = context;
        super.onAttach(context);
    }
}
