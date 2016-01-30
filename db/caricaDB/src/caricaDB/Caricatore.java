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
	
	public void connetti(){
		
	}
	
	public void caricaElementi() throws SQLException, ClassNotFoundException, IOException {
		PreparedStatement stmSql = null;
		int i = 0;
		String[] splitted = null;
		String s = null;
		
		fr = new FileReader("elementi.csv");
		leggi = new BufferedReader(fr);
		
		stmSql = conn.prepareStatement("INSERT INTO colori VALUES ?, ?");
		while((s=leggi.readLine()) != null){
			splitted = s.split(";");
			stmSql.setInt(1, Integer.parseInt(splitted[0]));
			stmSql.setString(2, splitted[1]);
		}
				
		fr.close();		
			
	}	
}
