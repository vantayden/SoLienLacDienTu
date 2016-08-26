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

public class FragmentSchedule extends Fragment {
    static String SANG = "sang";
    static String CHIEU = "chieu";
    static String NOTE = "note";
    static String ORI = "ori";
    int tenDP;
    TextView sang_title, chieu_title, note_title, sang_content, chieu_content, note_content;
    LinearLayout sang, chieu, note;


    public static final FragmentSchedule newInstance(String sang, String chieu, String note, int ori){
        FragmentSchedule t = new FragmentSchedule();
        Bundle bdl = new Bundle(3);
        bdl.putString(SANG, sang);
        bdl.putString(CHIEU, chieu);
        bdl.putString(NOTE, note);
        bdl.putInt(ORI, ori);
        t.setArguments(bdl);
        return t;
    }

    @Override
    public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {

        String content_sang = getArguments().getString(SANG);
        String content_chieu = getArguments().getString(CHIEU);
        String content_note = getArguments().getString(NOTE);
        int manHinh = getArguments().getInt(ORI);

        float scale = getResources().getDisplayMetrics().density;
        tenDP = (int) (10*scale + 0.5f);

        setText(container.getContext(), content_sang, content_chieu, content_note, manHinh);
        setLayout(container.getContext(), manHinh);
        LinearLayout.LayoutParams layoutSchedule, layoutBoc, bocLayout;
        LinearLayout schedule = new LinearLayout(container.getContext());
        LinearLayout boc = new LinearLayout(container.getContext());

        if(manHinh == Configuration.ORIENTATION_PORTRAIT) {
            layoutSchedule = new LinearLayout.LayoutParams(
                    LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT, 2f);
            layoutBoc = new LinearLayout.LayoutParams(
                    LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT, 1f);
            schedule.setOrientation(LinearLayout.VERTICAL);

            bocLayout = new LinearLayout.LayoutParams(
                    LinearLayout.LayoutParams.WRAP_CONTENT, LinearLayout.LayoutParams.MATCH_PARENT, 1f);
            boc.setOrientation(LinearLayout.HORIZONTAL);
        } else {
            layoutSchedule = new LinearLayout.LayoutParams(
                    LinearLayout.LayoutParams.WRAP_CONTENT, LinearLayout.LayoutParams.MATCH_PARENT, 2f);
            layoutBoc = new LinearLayout.LayoutParams(
                    LinearLayout.LayoutParams.WRAP_CONTENT, LinearLayout.LayoutParams.MATCH_PARENT, 1f);
            schedule.setOrientation(LinearLayout.HORIZONTAL);

            bocLayout = new LinearLayout.LayoutParams(
                    LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT, 1f);
            boc.setOrientation(LinearLayout.VERTICAL);
        }

        layoutSchedule.setMargins(tenDP, tenDP, tenDP, tenDP);
        layoutBoc.setMargins(tenDP, tenDP, tenDP, tenDP);
        bocLayout.setMargins(tenDP, tenDP, tenDP, tenDP);

        schedule.setBackgroundColor(getResources().getColor(R.color.material_grey_100));
        boc.setBackgroundColor(getResources().getColor(R.color.material_grey_100));

        boc.setWeightSum(2);
        boc.addView(sang, bocLayout);
        boc.addView(chieu, bocLayout);
        schedule.setWeightSum(3);
        schedule.addView(boc, layoutBoc);
        schedule.addView(note, layoutSchedule);

        return schedule;
    }

    public void setText(Context ct, String hs1, String hs2, String hs3, int ori){
        if(ori == Configuration.ORIENTATION_LANDSCAPE) {
            hs1 = hs1.replace("\n", " ");
            hs2 = hs2.replace("\n", " ");
            hs3 = hs3.replace("\n", " ");
        }
        sang_title = new TextView(ct);
        sang_title.setText("Sáng: ");
        sang_title.setTypeface(null, Typeface.BOLD);
        sang_title.setGravity(Gravity.CENTER);

        sang_content = new TextView(ct);
        sang_content.setText(hs1);
        sang_content.setGravity(Gravity.CENTER);

        chieu_title = new TextView(ct);
        chieu_title.setText("Chiều: ");
        chieu_title.setTypeface(null, Typeface.BOLD);
        chieu_title.setGravity(Gravity.CENTER);

        chieu_content = new TextView(ct);
        chieu_content.setText(hs2);
        chieu_content.setGravity(Gravity.CENTER);

        note_title = new TextView(ct);
        note_title.setText("Chú ý: ");
        note_title.setTypeface(null, Typeface.BOLD);
        note_title.setGravity(Gravity.CENTER);

        note_content = new TextView(ct);
        note_content.setText(hs3);
        note_content.setGravity(Gravity.CENTER);
    }

    public void setLayout(Context ct, int ori){
        chieu = new LinearLayout(ct);
        sang = new LinearLayout(ct);
        note = new LinearLayout(ct);
        if(ori == Configuration.ORIENTATION_PORTRAIT) {
            sang.setOrientation(LinearLayout.VERTICAL);
            chieu.setOrientation(LinearLayout.VERTICAL);
            note.setOrientation(LinearLayout.VERTICAL);
        } else {
            sang.setOrientation(LinearLayout.HORIZONTAL);
            chieu.setOrientation(LinearLayout.HORIZONTAL);
            note.setOrientation(LinearLayout.VERTICAL);
        }
        sang.setBackground(getResources().getDrawable(R.drawable.shadow));
        sang.setPadding(tenDP, tenDP, tenDP, tenDP);
        sang.setGravity(Gravity.CENTER);
        sang.addView(sang_title);
        sang.addView(sang_content);

        chieu.setBackground(getResources().getDrawable(R.drawable.shadow));
        chieu.setPadding(tenDP, tenDP, tenDP, tenDP);
        chieu.setGravity(Gravity.CENTER);
        chieu.addView(chieu_title);
        chieu.addView(chieu_content);

        note.setBackground(getResources().getDrawable(R.drawable.shadow));
        note.setPadding(tenDP, tenDP, tenDP, tenDP);
        note.setGravity(Gravity.CENTER);
        note.addView(note_title);
        note.addView(note_content);
    }
}
