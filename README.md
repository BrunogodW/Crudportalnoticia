# 📰 Portal de Notícias ÉHistória

## 📌 Descrição

Aplicação web em PHP e MySQL para gerenciamento de notícias com autenticação de usuário e painel administrativo exclusivo. O sistema oferece cadastro de usuários, publicação de notícias com upload de imagem, edição, exclusão e leitura de conteúdo.

## ✅ Funcionalidades principais

- Login e cadastro de usuário com validação de e-mail e senha
- Painel de usuário com histórico de notícias publicadas
- Criação de notícias com imagem opcional
- Edição de notícias com substituição de imagem
- Exclusão de notícias com remoção de imagem do servidor
- Visualização detalhada de notícia
- Exclusão de conta com remoção de notícias e imagens associadas
- Administrador com painel próprio e controle total do site
- Responsividade para desktop, tablet e celular
- Menu lateral com navegação limpa

## 🧱 Tecnologias

- PHP 7.x ou 8.x
- MySQL / MariaDB
- HTML5
- CSS3
- JavaScript
- PDO para acesso ao banco de dados

## 📁 Estrutura do projeto

```
/ (root)
  ├── classes/
  │   ├── Noticia.php
  │   └── Usuario.php
  ├── config/
  │   └── config.php
  ├── CSS/
  │   └── style.css
  ├── imagens/
  ├── dump.sql
  ├── index.php
  ├── login.php
  ├── cadastro.php
  ├── dashboard.php
  ├── admin_dashboard.php
  ├── nova_noticia.php
  ├── editar_noticia.php
  ├── editar_usuario.php
  ├── excluir_noticia.php
  ├── excluir_usuario.php
  ├── noticia.php
  ├── logout.php
  ├── verifica_login.php
  ├── script.js
  └── README.md
```

## 🔧 Configuração do banco de dados

O arquivo `dump.sql` cria o banco `portalnoticia`, as tabelas `usuarios` e `noticias`, e inclui dados de exemplo com um usuário admin.

### Como importar o banco

No phpMyAdmin ou no terminal MySQL:

```sql
mysql -u root -p < c:/xampp/htdocs/Crudportalnoticia-master/dump.sql
```

### Parâmetros de conexão

A conexão padrão está em `config/config.php`:

```php
$host = 'localhost';
$dbname = 'portalnoticia';
$username = 'root';
$password = '';
```

Ajuste esses valores se você usar outro usuário ou senha.

## 🧾 Estrutura das tabelas

### Tabela `usuarios`

- `id` INT(11) AUTO_INCREMENT: identificador do usuário
- `nome` VARCHAR(100): nome completo
- `email` VARCHAR(150): e-mail do usuário (único)
- `senha` VARCHAR(255): hash da senha
- `tipo` ENUM('admin','usuario'): define se o usuário é administrador ou comum

### Tabela `noticias`

- `id` INT(11) AUTO_INCREMENT: identificador da notícia
- `titulo` VARCHAR(255): título da notícia
- `noticia` TEXT: conteúdo completo
- `data` DATETIME: data de publicação
- `autor` INT(11): autor da notícia, chave estrangeira para `usuarios.id`
- `imagem` VARCHAR(255): nome do arquivo de imagem armazenado em `imagens/`

### Relação entre tabelas

- Um usuário possui muitas notícias
- Cada notícia pertence a um único autor
- `ON DELETE CASCADE` garante exclusão de notícias quando o usuário é removido

## 🔐 Permissões de usuário

### Admin

- Acessa `admin_dashboard.php`
- Pode visualizar, editar e excluir qualquer usuário
- Pode visualizar, editar e excluir qualquer notícia
- Tem painel próprio com estatísticas e gerenciamento de usuários

### Usuário comum

- Acessa `dashboard.php`
- Pode criar, editar e excluir apenas suas próprias notícias
- Pode editar e excluir apenas sua própria conta
- Não vê o painel administrativo nem ações de admin

## 👤 Conta administrativa padrão

O `dump.sql` já inclui um admin de exemplo.

- E-mail: `admin@ehistoria.com`
- Senha: `password`
- Tipo: `admin`

Se desejar criar manualmente outro admin, use:

```sql
INSERT INTO `usuarios` (`nome`, `email`, `senha`, `tipo`) VALUES
('Administrador', 'admin@ehistoria.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
```

## 🚀 Instalação local

1. Instale o XAMPP ou semelhante com Apache e MySQL.
2. Copie o projeto para `c:\xampp\htdocs\Crudportalnoticia-master`.
3. Importe `dump.sql` no banco de dados.
4. Ajuste `config/config.php` se necessário.
5. Abra no navegador:

```
http://localhost/Crudportalnoticia-master/index.php
```

## 🛠️ Uso da aplicação

### Acesso e navegação

- `index.php`: página pública com listagem de notícias
- `login.php`: formulário de autenticação
- `cadastro.php`: formulário de criação de conta
- `dashboard.php`: painel de usuário comum
- `admin_dashboard.php`: painel exclusivo do admin

### Notícias

- `nova_noticia.php`: criar notícia
- `editar_noticia.php`: editar notícia existente
- `excluir_noticia.php`: excluir notícia
- `noticia.php`: visualizar notícia completa

### Conta

- `editar_usuario.php`: editar perfil
- `excluir_usuario.php`: excluir conta ou usuário (admin)
- `logout.php`: encerrar sessão

## 💾 Banco de dados e manutenção

### Caso já exista `usuarios` sem coluna `tipo`

```sql
ALTER TABLE `usuarios`
ADD COLUMN `tipo` ENUM('admin','usuario') NOT NULL DEFAULT 'usuario';
```

### Consulta útil para listar notícias com autor

```sql
SELECT n.id, n.titulo, n.data, u.nome AS autor
FROM noticias n
JOIN usuarios u ON u.id = n.autor
ORDER BY n.data DESC;
```

## 📌 Observações importantes

- Senhas são armazenadas como hash seguro com `password_hash()`.
- As imagens são salvas em `imagens/` e o nome do arquivo é salvo no banco.
- Exclusão de conta apaga notícias e imagens associadas.
- O menu lateral é a navegação principal e o header mostra apenas o usuário logado.
- O sistema é responsivo para desktop, tablet e celular.

## ✅ Testes recomendados

- Login com admin e usuário comum
- Cadastro de usuário
- Criação, edição e exclusão de notícia
- Exclusão de conta com logout automático
- Verificação de permissões admin e usuário comum

## 🔄 Possíveis melhorias

- adicionar categorias de notícias
- implementar pesquisa e filtros
- incluir paginação nas listagens
- melhorar validação de upload de imagem (MIME type)
- adicionar recuperação de senha e confirmação por e-mail
