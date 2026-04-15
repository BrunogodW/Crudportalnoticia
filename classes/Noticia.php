<?php
class Noticia
{
    private $db;
    private $tabela = 'noticias';

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function criar($titulo, $noticia, $autor, $imagem = null)
    {
        $query = "INSERT INTO {$this->tabela} (titulo, noticia, autor, imagem, data) VALUES (:titulo, :noticia, :autor, :imagem, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':noticia', $noticia);
        $stmt->bindParam(':autor', $autor);
        $stmt->bindParam(':imagem', $imagem);
        return $stmt->execute();
    }

    public function ler()
    {
        $query = "SELECT n.*, u.nome as nome_autor FROM {$this->tabela} n 
                  JOIN usuarios u ON n.autor = u.id 
                  ORDER BY n.data DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function lerPorId($id)
    {
        $query = "SELECT n.*, u.nome as nome_autor FROM {$this->tabela} n 
                  JOIN usuarios u ON n.autor = u.id 
                  WHERE n.id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function lerPorAutor($autorId)
    {
        $query = "SELECT * FROM {$this->tabela} WHERE autor = :autor ORDER BY data DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':autor', $autorId);
        $stmt->execute();
        return $stmt;
    }

    public function atualizar($id, $titulo, $noticia, $imagem = null)
    {
        if ($imagem) {
            $query = "UPDATE {$this->tabela} SET titulo = :titulo, noticia = :noticia, imagem = :imagem WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':imagem', $imagem);
        } else {
            $query = "UPDATE {$this->tabela} SET titulo = :titulo, noticia = :noticia WHERE id = :id";
            $stmt = $this->db->prepare($query);
        }
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':noticia', $noticia);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function excluir($id)
    {
        $query = "DELETE FROM {$this->tabela} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function pertenceAoAutor($id, $autorId)
    {
        $query = "SELECT id FROM {$this->tabela} WHERE id = :id AND autor = :autor LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':autor', $autorId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function ultimoId()
    {
        return $this->db->lastInsertId();
    }
}
