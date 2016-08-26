package vn.piti.draku.piti;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;
import java.util.ArrayList;

import vn.piti.draku.piti.DoiTuong.Notification;

public class CustomNotificationListAdapter extends BaseAdapter {
    private ArrayList<Notification> listData;
    private LayoutInflater layoutInflater;

    public CustomNotificationListAdapter(Context aContext, ArrayList<Notification> listData) {
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

    public View getView(int position, View convertView, ViewGroup parent) {
        ViewHolder holder;
        if (convertView == null) {
            convertView = layoutInflater.inflate(R.layout.single_notification, null);
            holder = new ViewHolder();
            holder.headlineView = (TextView) convertView.findViewById(R.id.content);
            holder.reporterNameView = (TextView) convertView.findViewById(R.id.teacher);
            holder.reportedDateView = (TextView) convertView.findViewById(R.id.date);
            convertView.setTag(holder);
        } else {
            holder = (ViewHolder) convertView.getTag();
        }

        holder.headlineView.setText(listData.get(position).getContent());
        holder.reporterNameView.setText("Giáo viên " + listData.get(position).getTeacher());
        holder.reportedDateView.setText(listData.get(position).getDate());
        return convertView;
    }

    static class ViewHolder {
        TextView headlineView;
        TextView reporterNameView;
        TextView reportedDateView;
    }
}
