package caricaDB;

import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.SQLException;
import java.sql.Types;

public class Caricatore {
	private Connection conn = null;
	private FileReader fr = null;
	private BufferedReader leggi = null;
	
	
	public Caricatore(Connection conn){
		this.conn = conn;
	}
	
	/** Carica una tabella del db composta da un attributo
	il contenuto di un file csv ricevuto in input
	@param tabella - la tabella da caricare
	@param file - il file da cui leggere
	*/
	public void carica(String tabella, String file) throws SQLException, ClassNotFoundException, IOException {
		PreparedStatement stmSql = null;
		int i = 0;
		String s = null;
		
		/*Lettura del file contenente i dati da caricare*/
		fr = new FileReader(file);
		leggi = new BufferedReader(fr);
		
		/*Preparazione della query, successivamente faccio la bind dei parametri.
		'conn' è un attributo della classe*/
		stmSql = conn.prepareStatement("INSERT INTO " + tabella + " VALUES (?, ?)");
		/*Fino a quando il file non termina carico il contenuto di ogni riga*/
		while((s=leggi.readLine()) != null){
			/*Il primo attributo è un auto-increment, pertanto lo valorizzo a null*/
			stmSql.setNull(1, Types.INTEGER);
			stmSql.setString(2, s);
			
			stmSql.executeUpdate();
			System.out.println(i++);
		}
				
		fr.close();		
			
	}		
}
