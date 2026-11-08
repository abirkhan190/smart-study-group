import javax.swing.*;
import javax.swing.table.DefaultTableModel;
import java.awt.*;
import java.sql.*;

public class StudyGroupAdmin extends JFrame {
    static final String URL = "jdbc:mysql://localhost:3306/smart_study";
    static final String USER = "root", PASS = "";   // password thakle ekhane dao

    DefaultTableModel users = model("ID", "Name", "Email");
    DefaultTableModel groups = model("ID", "Group", "Subject", "Leader", "Members");
    DefaultTableModel tasks = model("ID", "Group", "Title", "Deadline", "Status");
    JTable groupTable = new JTable(groups), taskTable = new JTable(tasks);
    JTextField search = new JTextField(20);

    static DefaultTableModel model(String... cols) {
        return new DefaultTableModel(cols, 0) {
            public boolean isCellEditable(int r, int c) { return false; }
        };
    }

    void load(DefaultTableModel m, String sql, Object... p) {
        m.setRowCount(0);
        try (Connection c = DriverManager.getConnection(URL, USER, PASS);
             PreparedStatement ps = c.prepareStatement(sql)) {
            for (int i = 0; i < p.length; i++) ps.setObject(i + 1, p[i]);
            ResultSet rs = ps.executeQuery();
            int n = rs.getMetaData().getColumnCount();
            while (rs.next()) {
                Object[] row = new Object[n];
                for (int i = 0; i < n; i++) row[i] = rs.getObject(i + 1);
                m.addRow(row);
            }
        } catch (SQLException e) { error(e); }
    }

    void run(String sql, Object... p) {
        try (Connection c = DriverManager.getConnection(URL, USER, PASS);
             PreparedStatement ps = c.prepareStatement(sql)) {
            for (int i = 0; i < p.length; i++) ps.setObject(i + 1, p[i]);
            ps.executeUpdate();
        } catch (SQLException e) { error(e); }
    }

    void error(Exception e) {
        JOptionPane.showMessageDialog(this, "Database error: " + e.getMessage());
    }

    void refresh() {
        String q = "%" + search.getText().trim() + "%";
        load(users, "SELECT id,name,email FROM users");
        load(groups, "SELECT g.id,g.name,g.subject,u.name,(SELECT COUNT(*) FROM group_members WHERE group_id=g.id) " +
                "FROM study_groups g JOIN users u ON u.id=g.leader_id WHERE g.name LIKE ? OR g.subject LIKE ?", q, q);
        load(tasks, "SELECT t.id,g.name,t.title,t.deadline,t.status FROM tasks t JOIN study_groups g ON g.id=t.group_id");
    }

    StudyGroupAdmin() {
        super("Smart Study Group - Admin Panel");
        JButton find = new JButton("Search"), del = new JButton("Delete Group"),
                toggle = new JButton("Toggle Status"), reload = new JButton("Refresh");
        find.addActionListener(e -> refresh());
        reload.addActionListener(e -> refresh());
        del.addActionListener(e -> {
            int r = groupTable.getSelectedRow();
            if (r < 0) { JOptionPane.showMessageDialog(this, "Select a group first"); return; }
            if (JOptionPane.showConfirmDialog(this, "Delete this group?", "Confirm",
                    JOptionPane.YES_NO_OPTION) == JOptionPane.YES_OPTION) {
                run("DELETE FROM study_groups WHERE id=?", groups.getValueAt(r, 0));
                refresh();
            }
        });
        toggle.addActionListener(e -> {
            int r = taskTable.getSelectedRow();
            if (r < 0) { JOptionPane.showMessageDialog(this, "Select a task first"); return; }
            run("UPDATE tasks SET status=IF(status='Pending','Completed','Pending') WHERE id=?", tasks.getValueAt(r, 0));
            refresh();
        });

        JPanel gTop = new JPanel(new FlowLayout(FlowLayout.LEFT));
        gTop.add(new JLabel("Search:")); gTop.add(search); gTop.add(find); gTop.add(del);
        JPanel gPanel = new JPanel(new BorderLayout());
        gPanel.add(gTop, BorderLayout.NORTH);
        gPanel.add(new JScrollPane(groupTable), BorderLayout.CENTER);

        JPanel tPanel = new JPanel(new BorderLayout());
        tPanel.add(new JScrollPane(taskTable), BorderLayout.CENTER);
        JPanel tBottom = new JPanel(new FlowLayout(FlowLayout.LEFT));
        tBottom.add(toggle);
        tPanel.add(tBottom, BorderLayout.SOUTH);

        JTabbedPane tabs = new JTabbedPane();
        tabs.addTab("Groups", gPanel);
        tabs.addTab("Tasks", tPanel);
        tabs.addTab("Users", new JScrollPane(new JTable(users)));

        add(tabs, BorderLayout.CENTER);
        JPanel bottom = new JPanel(); bottom.add(reload);
        add(bottom, BorderLayout.SOUTH);
        setSize(800, 450);
        setDefaultCloseOperation(EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        refresh();
    }

    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> new StudyGroupAdmin().setVisible(true));
    }
}
