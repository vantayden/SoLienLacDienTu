package vn.piti.draku.piti;


import android.util.Log;

import org.apache.http.HttpResponse;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.client.HttpClient;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.protocol.HTTP;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;

public class DoPost {
    public String POST(String url, String data) {
        InputStream inputStream;
        String result = new String("");
        try {
            // create HttpClient
            HttpClient httpclient = new DefaultHttpClient();

            //make POST request to the given URL
            HttpPost httpPost = new HttpPost(url);

            //set json to StringEntity
            StringEntity se = new StringEntity(data, HTTP.UTF_8);

            //set httpPost Entity
            httpPost.setEntity(se);

            //Set some headers to inform server about the type of the content
            httpPost.setHeader("Accept", "application/json");
            httpPost.setHeader("Content-type", "application/json");

            //Execute POST request to the given URL
            HttpResponse httpResponse = httpclient.execute(httpPost);

            //receive response as inputStream
            inputStream = httpResponse.getEntity().getContent();

            //convert inputstream to string
            if (inputStream != null)
                result = convertInputStreamToString(inputStream);
            else
                result = "Did not work!";

        } catch (Exception e) {
            Log.d("InputStream", result);
        }

        return result;
    }

    private static String convertInputStreamToString(InputStream inputStream) throws
            IOException {
        BufferedReader bufferedReader = new BufferedReader( new InputStreamReader(inputStream));
        String line;
        String result = new String("");
        while((line = bufferedReader.readLine()) != null)
            result += line;

        inputStream.close();
        return result;

    }
}

