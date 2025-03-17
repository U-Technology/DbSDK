# 🚀 DbSDK – Il tuo ORM per PHP




DbSDK è un **ORM leggero e potente per PHP** che semplifica la gestione del database eliminando la necessità di scrivere query SQL manualmente.\
Attualmente supporta **MySQL**, con il supporto a **PostgreSQL** in fase di sviluppo.

---

## 🌟 Caratteristiche principali

✅ **Gestione automatica delle query** (`INSERT`, `UPDATE`, `DELETE`, `SELECT`)\
✅ **Supporto per MySQL** (PostgreSQL in arrivo)\
✅ **Configurazione minimale**\
✅ **Semplice e intuitivo** – usa le classi come tabelle senza query SQL

---

## 📚 Installazione

Installa DbSDK con **Composer**:

```sh
composer require utechnology/dbsdk
```

---

## 🔧 Configurazione

Configura il database nel tuo progetto:

```php
use UTechnology\DbSDK\ConfigConnection;

ConfigConnection::settingConnectionMySQL(
                                        'localhost',
                                        'nome_database',
                                        'utente', 
                                        'password'
);
```

---

## 🛠 Esempio d'uso

### **1️⃣ Creazione di un modello**

```php
use FieldName;
use IsAutoIncrement;
use IsPrimaryKeyField;
use TableName;
use UTechnology\DbSDK\__EntityDB;

#[TableName('UserAssociation')]
class User extends __EntityDB
{
    public function __construct(mixed $ID = null) {
        parent::__construct($ID);
    }

    #[IsPrimaryKeyField]
    #[IsAutoIncrement]
    #[FieldName('ID')]
    public int $ID;

    #[FieldName('Name')]
    public string $name;

    #[FieldName('Email')]
    public string $email;
}
```

### **2️⃣ Inserimento di un record**

```php
$user = new User();
$user->name = "Mario Rossi";
$user->email = "mario@example.com";
$user->__save();  // INSERT automatico
```

### **3️⃣ Selezione di un record**

```php
$user = new User(1);  // Trova l'utente con ID 1
echo $user->name;
```

### **4️⃣ Aggiornamento di un record**

```php
$user->email = "nuovaemail@example.com";
$user->__save();  // UPDATE automatico
```

### **5️⃣ Eliminazione di un record**

```php
$user->__delete();  // DELETE automatico
```

---

## 📖 Documentazione

Consulta la [documentazione completa](https://github.com/utechnology/dbsdk/wiki) per dettagli su tutte le funzionalità.

---

## 💜 Licenza

DbSDK è rilasciato sotto la licenza [MIT](LICENSE).

---

# 🌍 English Version

# 🚀 DbSDK – Your PHP ORM

\
\


DbSDK is a **lightweight and powerful PHP ORM** that simplifies database management by eliminating the need to write SQL queries manually.\
Currently supports **MySQL**, with **PostgreSQL** support in development.

---

## 🌟 Key Features

✅ **Automatic query handling** (`INSERT`, `UPDATE`, `DELETE`, `SELECT`)\
✅ **MySQL support** (PostgreSQL coming soon)\
✅ **Minimal configuration**\
✅ **Simple and intuitive** – use classes as database tables without writing SQL

---

## 📚 Installation

Install DbSDK via **Composer**:

```sh
composer require utechnology/dbsdk
```

---

## 🔧 Configuration

Set up your database connection:

```php
use UTechnology\DbSDK\ConfigConnection;

ConfigConnection::settingConnectionMySQL(
                                        'localhost',
                                        'nome_database',
                                        'utente', 
                                        'password'
);
```

---

## 🛠 Usage Example

### **1️⃣ Creating a Model**

```php
use FieldName;
use IsAutoIncrement;
use IsPrimaryKeyField;
use TableName;
use UTechnology\DbSDK\__EntityDB;

#[TableName('UserAssociation')]
class User extends __EntityDB
{
    public function __construct(mixed $ID = null) {
        parent::__construct($ID);
    }

    #[IsPrimaryKeyField]
    #[IsAutoIncrement]
    #[FieldName('ID')]
    public int $ID;

    #[FieldName('Name')]
    public string $name;

    #[FieldName('Email')]
    public string $email;
}
```

### **2️⃣ Inserting a Record**

```php
$user = new User();
$user->name = "Mario Rossi";
$user->email = "mario@example.com";
$user->__save();  // Automatic INSERT
```

### **3️⃣ Selecting a Record**

```php
$user = new User(1);  // Find user with ID 1
echo $user->name;
```

### **4️⃣ Updating a Record**

```php
$user->email = "nuovaemail@example.com";
$user->__save();  // Automatic UPDATE
```

### **5️⃣ Deleting a Record**

```php
$user->__delete();  // Automatic DELETE
```

---

## 📖 Documentation

Check out the [full documentation](https://github.com/utechnology/dbsdk/wiki) for more details.

---

## 💜 License

DbSDK is released under the [MIT License](LICENSE).

