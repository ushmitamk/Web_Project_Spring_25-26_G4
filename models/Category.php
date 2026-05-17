<?php
class Category
{
    public static function all()
    {
        return Database::run(
            "SELECT c.*, p.name AS parent_name
             FROM categories c
             LEFT JOIN categories p ON c.parent_id = p.id
             ORDER BY COALESCE(c.parent_id, c.id), c.parent_id IS NOT NULL, c.name"
        )->fetchAll();
    }

    public static function hierarchyOptions()
    {
        $rows = self::all();
        $roots = [];
        $children = [];
        foreach ($rows as $row) {
            if (empty($row['parent_id'])) {
                $roots[] = $row;
            } else {
                $children[(int)$row['parent_id']][] = $row;
            }
        }
        $options = [];
        foreach ($roots as $root) {
            $root['label'] = $root['name'];
            $options[] = $root;
            $rootId = (int)$root['id'];
            if (!empty($children[$rootId])) {
                foreach ($children[$rootId] as $child) {
                    $child['label'] = '— ' . $child['name'];
                    $options[] = $child;
                }
            }
        }
        return $options;
    }

    public static function rootOptions($excludeId = null)
    {
        if ($excludeId) {
            return Database::run(
                "SELECT * FROM categories WHERE parent_id IS NULL AND id <> ? ORDER BY name",
                [(int)$excludeId]
            )->fetchAll();
        }
        return Database::run("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name")->fetchAll();
    }

    public static function find($id)
    {
        return Database::run("SELECT * FROM categories WHERE id = ?", [(int)$id])->fetch();
    }

    public static function create($name, $parentId)
    {
        Database::run("INSERT INTO categories (name, parent_id) VALUES (?, ?)", [$name, $parentId ?: null]);
    }

    public static function update($id, $name, $parentId)
    {
        Database::run("UPDATE categories SET name = ?, parent_id = ? WHERE id = ?", [$name, $parentId ?: null, (int)$id]);
    }

    public static function hasChildren($id)
    {
        return (int)Database::run("SELECT COUNT(*) FROM categories WHERE parent_id = ?", [(int)$id])->fetchColumn() > 0;
    }

    public static function hasProducts($id)
    {
        return (int)Database::run("SELECT COUNT(*) FROM products WHERE category_id = ?", [(int)$id])->fetchColumn() > 0;
    }

    public static function delete($id)
    {
        Database::run("DELETE FROM categories WHERE id = ?", [(int)$id]);
    }
}
