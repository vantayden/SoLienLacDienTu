package vn.piti.draku.piti;

import android.content.Context;
import android.content.res.Configuration;
import android.graphics.Typeface;
import android.os.Bundle;
import android.support.annotation.Nullable;
import android.support.v4.app.Fragment;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;

public class FragmentMark extends Fragment {
    static String HE_SO_1= "hs1";
    static String HE_SO_2 = "hs2";
    static String HE_SO_3 = "hs3";
    static String ORI = "ori";
    int tenDP;
    TextView hs1_title, hs2_title, hs3_title, hs1_diem, hs2_diem, hs3_diem;
    LinearLayout hs1, hs2, hs3;


    public static FragmentMark newInstance(String hs1, String hs2, String hs3, int ori){
        FragmentMark t = new FragmentMark();
        Bundle bdl = new Bundle(3);
        bdl.putString(HE_SO_1, hs1);
        bdl.putString(HE_SO_2, hs2);
        bdl.putString(HE_SO_3, hs3);
        bdl.putInt(ORI, ori);
        t.setArguments(bdl);
        return t;
    }

    @Override
    public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {

        String diem_hs1 = getArguments().getString(HE_SO_1);
        String diem_hs2 = getArguments().getString(HE_SO_2);
        String diem_hs3 = getArguments().getString(HE_SO_3);
        int manHinh = getArguments().getInt(ORI);

        float scale = getResources().getDisplayMetrics().density;
        tenDP = (int) (10*scale + 0.5f);

        setText(container.getContext(), diem_hs1, diem_hs2, diem_hs3);
        setLayout(container.getContext());
        LinearLayout.LayoutParams layoutParams;
        LinearLayout mark = new LinearLayout(container.getContext());

        if(manHinh == Configuration.ORIENTATION_PORTRAIT) {
            layoutParams = new LinearLayout.LayoutParams(
                    LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT, 1f);
            mark.setOrientation(LinearLayout.VERTICAL);
        } else {
            layoutParams = new LinearLayout.LayoutParams(
                    LinearLayout.LayoutParams.WRAP_CONTENT, LinearLayout.LayoutParams.MATCH_PARENT, 1f);
            mark.setOrientation(LinearLayout.HORIZONTAL);
        }

        layoutParams.setMargins(tenDP, tenDP, tenDP, tenDP);
        mark.setBackgroundColor(getResources().getColor(R.color.material_grey_100));
        mark.setWeightSum(3);

        mark.addView(hs1, layoutParams);
        mark.addView(hs2, layoutParams);
        mark.addView(hs3, layoutParams);

        return mark;
    }

    public void setText(Context ct, String hs1, String hs2, String hs3){
        hs1_title = new TextView(ct);
        hs1_title.setText("Điểm hệ số 1(miệng, 15') ");
        hs1_title.setTypeface(null, Typeface.BOLD);
        hs1_title.setGravity(Gravity.CENTER);

        hs1_diem = new TextView(ct);
        hs1_diem.setText(hs1);
        hs1_diem.setTextColor(getResources().getColor(R.color.material_red_400));
        hs1_diem.setGravity(Gravity.CENTER);

        hs2_title = new TextView(ct);
        hs2_title.setText("Điểm hệ số 2(45', giữa kỳ) ");
        hs2_title.setTypeface(null, Typeface.BOLD);
        hs2_title.setGravity(Gravity.CENTER);

        hs2_diem = new TextView(ct);
        hs2_diem.setText(hs2);
        hs2_diem.setTextColor(getResources().getColor(R.color.material_red_400));
        hs2_diem.setGravity(Gravity.CENTER);

        hs3_title = new TextView(ct);
        hs3_title.setText("Điểm hệ số 3(cuối kì) ");
        hs3_title.setTypeface(null, Typeface.BOLD);
        hs3_title.setGravity(Gravity.CENTER);

        hs3_diem = new TextView(ct);
        hs3_diem.setText(hs3);
        hs3_diem.setTextColor(getResources().getColor(R.color.material_red_400));
        hs3_diem.setGravity(Gravity.CENTER);
    }

    public void setLayout(Context ct){
        hs1 = new LinearLayout(ct);
        hs1.setOrientation(LinearLayout.VERTICAL);
        hs1.setBackground(getResources().getDrawable(R.drawable.shadow));
        hs1.setPadding(tenDP, tenDP, tenDP, tenDP);
        hs1.setGravity(Gravity.CENTER);
        hs1.addView(hs1_title);
        hs1.addView(hs1_diem);

        hs2 = new LinearLayout(ct);
        hs2.setOrientation(LinearLayout.VERTICAL);
        hs2.setBackground(getResources().getDrawable(R.drawable.shadow));
        hs2.setPadding(tenDP, tenDP, tenDP, tenDP);
        hs2.setGravity(Gravity.CENTER);
        hs2.addView(hs2_title);
        hs2.addView(hs2_diem);

        hs3 = new LinearLayout(ct);
        hs3.setOrientation(LinearLayout.VERTICAL);
        hs3.setBackground(getResources().getDrawable(R.drawable.shadow));
        hs3.setPadding(tenDP, tenDP, tenDP, tenDP);
        hs3.setGravity(Gravity.CENTER);
        hs3.addView(hs3_title);
        hs3.addView(hs3_diem);
    }
}
