package caricaDB;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.text.ParseException;



public class Inizio {
	public static void main(String[] args) throws SQLException, ClassNotFoundException, ParseException{
		//per connettermi al db principale
		String stringaConn = null;
		Connection conn = null;
		PreparedStatement stmSql = null;
		//per caricare il db
		Caricatore carica = null;
		
		
		/*Si connette a mydb, va cambiato*/
		stringaConn = "jdbc:mysql://localhost/carte?user=root&password=";
		
		//connessione a mydb
		Class.forName("com.mysql.jdbc.Driver");
		conn = DriverManager.getConnection(stringaConn);	
		
		carica = new Caricatore(conn);

		
		
		try {
			conn.close();
			//connComuni.close();
			System.out.println("alleeeeee");
		} catch (SQLException e) {
			System.out.println("Error closing connection");
		}
	}

}

