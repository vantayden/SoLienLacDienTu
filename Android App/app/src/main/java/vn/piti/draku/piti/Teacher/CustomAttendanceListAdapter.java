package vn.piti.draku.piti.Teacher;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.CheckBox;
import android.widget.CompoundButton;
import android.widget.TextView;

import java.util.ArrayList;

import vn.piti.draku.piti.Objects.Attendance;
import vn.piti.draku.piti.R;

public class CustomAttendanceListAdapter extends BaseAdapter {
    private ArrayList<Attendance> listData;
    private LayoutInflater layoutInflater;
    ViewHolder holder;

    public CustomAttendanceListAdapter(Context aContext, ArrayList<Attendance> listData) {
        this.listData = listData;
        layoutInflater = LayoutInflater.from(aContext);
    }

    @Override
    public int getCount() {
        return listData.size();
    }

    @Override
    public Object getItem(int position) {
        return listData.get(position);
    }

    @Override
    public long getItemId(int position) {
        return position;
    }

    public View getView(final int position, View convertView, ViewGroup parent) {
        if (convertView == null) {
            convertView = layoutInflater.inflate(R.layout.single_attendance_student, null);
            holder = new ViewHolder();
            holder.check = (CheckBox) convertView.findViewById(R.id.checkattendance);
            holder.student = (TextView) convertView.findViewById(R.id.student);
            holder.reason = (TextView) convertView.findViewById(R.id.reason);
            holder.student.setText(listData.get(position).getStudent());
            switch(listData.get(position).getStatus()){
                case 0:
                    holder.check.setChecked(false);
                    break;
                case 1:
                    holder.check.setChecked(true);
                    break;
                case 2:
                    holder.reason.setText(listData.get(position).getReason());
                    holder.reason.setVisibility(View.VISIBLE);
                    holder.check.setEnabled(false);
                    break;
            }

            holder.check.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener()
            {
                @Override
                public void onCheckedChanged(CompoundButton buttonView, boolean isChecked)
                {
                    if (!isChecked)
                        listData.get(position).setStatus(0);
                    else
                        listData.get(position).setStatus(1);
                }
            });
            convertView.setTag(holder);
        } else {
            holder = (ViewHolder) convertView.getTag();
        }



        return convertView;
    }

    static class ViewHolder {
        TextView student;
        TextView reason;
        CheckBox check;
    }
}
