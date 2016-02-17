package caricaDB;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.sql.Connection;
import java.sql.Date;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.HashMap;

public class Caricatore {
	private Connection conn = null;
	private FileReader fr = null;
	private BufferedReader leggi = null;
	
	public Caricatore(Connection conn){
		this.conn = conn;
	}
	
	
	public void carica(String tabella, String file) throws SQLException, ClassNotFoundException, IOException {
		PreparedStatement stmSql = null;
		int i = 0;
		String s = null;
		
		fr = new FileReader(file);
		leggi = new BufferedReader(fr);
		
		stmSql = conn.prepareStatement("INSERT INTO " + tabella + " VALUES (?, ?)");
		while((s=leggi.readLine()) != null){
			stmSql.setInt(1, 0);
			stmSql.setString(2, s);
			
			stmSql.executeUpdate();
		}
				
		fr.close();		
			
	}		
}
