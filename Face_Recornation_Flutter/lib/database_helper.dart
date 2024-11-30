import 'package:sqflite/sqflite.dart';
import 'package:path/path.dart';
import 'package:crypto/crypto.dart';
import 'dart:convert';
import 'user.dart';

class DatabaseHelper {
  static final DatabaseHelper _instance = DatabaseHelper._internal();
  static Database? _database;

  DatabaseHelper._internal();

  factory DatabaseHelper() {
    return _instance;
  }

  Future<Database> get database async {
    if (_database != null) return _database!;
    _database = await _initDatabase();
    return _database!;
  }

  Future<Database> _initDatabase() async {
    String path = join(await getDatabasesPath(), 'users.db');
    return await openDatabase(
      path,
      version: 2, // Increment the version number to trigger onUpgrade
      onCreate: (db, version) {
        return db.execute(
          'CREATE TABLE users(uid TEXT PRIMARY KEY, username TEXT, email TEXT, password TEXT, created_at TEXT)',
        );
      },
      onUpgrade: (db, oldVersion, newVersion) {
        if (oldVersion < 2) {
          db.execute('ALTER TABLE users ADD COLUMN password TEXT');
        }
      },
    );
  }

  String hashPassword(String password) {
    return md5.convert(utf8.encode(password)).toString(); // Ganti dengan metode hashing yang lebih aman
  }

  Future<void> insertUser(Map<String, dynamic> user) async {
    final db = await database;
    await db.insert(
      'users',
      user,
      conflictAlgorithm: ConflictAlgorithm.replace,
    );
  }

  Future<List<Map<String, dynamic>>> getUsers() async {
    final db = await database;
    return await db.query('users');
  }

  Future<Map<String, dynamic>?> getUser(String username, String password) async {
    final db = await database;
    final hashedPassword = hashPassword(password); // Hash password sebelum query
    final List<Map<String, dynamic>> results = await db.query(
      'users',
      where: 'username = ? AND password = ?',
      whereArgs: [username, hashedPassword],
    );

    if (results.isNotEmpty) {
      return results.first;
    } else {
      return null;
}
}

  validateUser(String username, String password) {}
}
