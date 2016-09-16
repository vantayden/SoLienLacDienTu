package vn.piti.draku.piti;

import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.media.Image;
import android.app.FragmentManager;
import android.os.Bundle;
import android.support.v7.app.AlertDialog;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.squareup.picasso.Picasso;

import java.util.ArrayList;

import vn.piti.draku.piti.DoiTuong.Notification;

public class CustomNotificationListAdapter extends BaseAdapter {
    private ArrayList<Notification> listData;
    private LayoutInflater layoutInflater;
    private Context ct;
    private FragmentManager fm;
    AppConfig config;

    public CustomNotificationListAdapter(Context aContext, ArrayList<Notification> listData, FragmentManager fm) {
        this.listData = listData;
        layoutInflater = LayoutInflater.from(aContext);
        this.ct = aContext;
        this.fm = fm;
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
        final ViewHolder holder;
        if (convertView == null) {
            convertView = layoutInflater.inflate(R.layout.single_notification, null);
            holder = new ViewHolder();
            holder.headlineView = (TextView) convertView.findViewById(R.id.content);
            holder.reporterNameView = (TextView) convertView.findViewById(R.id.teacher);
            holder.reportedDateView = (TextView) convertView.findViewById(R.id.date);
            holder.reportedImageView = (ImageView) convertView.findViewById(R.id.teacher_image);

            convertView.setTag(holder);
        } else {
            holder = (ViewHolder) convertView.getTag();
        }

        holder.headlineView.setText(listData.get(position).getContent());
        holder.reporterNameView.setText("Giáo viên " + listData.get(position).getTeacherName());
        holder.reportedDateView.setText(listData.get(position).getDate());

        final FragmentTeacherInfo dialogFragment = new FragmentTeacherInfo();

        Bundle teacherInfo = new Bundle();
        String[] teacherArray = listData.get(position).getTeacherInfo();
        teacherInfo.putStringArray("teacher_info", teacherArray);
        dialogFragment.setArguments(teacherInfo);

        holder.reportedImageView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                dialogFragment.show(fm, "Thông tin giáo viên");
            }
        });
        if(!listData.get(position).getTeacherImage().equals(""))
            Picasso.with(this.ct).load(config.IMAGE_URL + listData.get(position).getTeacherImage()).into(holder.reportedImageView);


        return convertView;
    }

    static class ViewHolder {
        TextView headlineView;
        TextView reporterNameView;
        TextView reportedDateView;
        ImageView reportedImageView;
    }
}
