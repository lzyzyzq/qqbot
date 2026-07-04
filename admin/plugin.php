<?php
if (!isset($_COOKIE['admin_token'])) {
    header("Location: index.php");
    exit();
}

$appid = $_GET['appid'] ?? '';
if (empty($appid)) {
    die("缺少appid参数");
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>月下独酌管机 · 插件管理 - <?php echo htmlspecialchars($appid); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after {
            margin: 0; padding: 0; box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }
        :focus { outline: none; }

        :root {
            --bg: #f8fafc;
            --card: #ffffff;
            --border: #e9edf2;
            --text-main: #1a2c3e;
            --text-sub: #5e6f8d;
            --text-muted: #8b9ab0;
            --primary: #2c6b9e;
            --primary-hover: #235b87;
            --success: #2c6e2c;
            --warning: #b85c1a;
            --danger: #c23d2e;
            --sidebar-width: 240px;
            --header-height: 52px;
        }

        body {
            background: var(--bg);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-main);
            line-height: 1.5;
            overflow-x: hidden;
            width: 100%;
        }

        .desktop-layout { display: flex; min-height: 100vh; }
        .sidebar {
            width: var(--sidebar-width);
            background: var(--card);
            border-right: 1px solid var(--border);
            position: fixed; top: 0; bottom: 0; left: 0;
            display: flex; flex-direction: column;
            z-index: 100;
        }
        .sidebar-header { padding: 20px 24px; border-bottom: 1px solid var(--border); }
        .sidebar-header h1 { font-size: 18px; font-weight: 600; }
        .sidebar-header p { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
        .sidebar-nav { flex: 1; padding: 16px 0; overflow-y: auto; }
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 24px; color: var(--text-sub);
            text-decoration: none; font-size: 14px; font-weight: 500;
            transition: all 0.15s; cursor: pointer;
        }
        .nav-item:hover { background: #f1f5f9; color: var(--primary); }
        .nav-item.active { background: #f1f5f9; color: var(--primary); border-left: 3px solid var(--primary); padding-left: 21px; }
        .nav-item i { width: 20px; font-size: 15px; }
        .sidebar-footer { padding: 16px 24px; border-top: 1px solid var(--border); font-size: 11px; color: var(--text-muted); }
        
        .main-content {
            flex: 1; margin-left: var(--sidebar-width); min-height: 100vh;
            min-width: 0; overflow-x: hidden;
        }
        .top-bar {
            background: var(--card); border-bottom: 1px solid var(--border);
            padding: 0 32px; height: var(--header-height);
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 10;
        }
        .page-title { font-size: 15px; font-weight: 500; }
        .container {
            padding: 28px 32px; max-width: 1400px;
            overflow-x: hidden;
        }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: var(--card); border: 1px solid var(--border); border-radius: 10px; padding: 14px 18px; }
        .stat-label { font-size: 12px; color: var(--text-muted); margin-bottom: 6px; }
        .stat-value { font-size: 26px; font-weight: 600; line-height: 1.2; }
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 10px; overflow: hidden; }
        .card-header {
            padding: 14px 20px; border-bottom: 1px solid var(--border);
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 12px;
        }
        .card-header h2 { font-size: 16px; font-weight: 600; }
        .tabs { display: flex; gap: 8px; }
        .tab {
            padding: 6px 14px; font-size: 13px; font-weight: 500;
            border-radius: 20px; cursor: pointer;
            background: #f1f5f9; color: var(--text-sub);
            transition: all 0.15s;
        }
        .tab.active { background: var(--primary); color: white; }
        .tab-content { display: none; padding: 20px; }
        .tab-content.active { display: block; }
        .plugin-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px; }
        .plugin-card { border: 1px solid var(--border); border-radius: 10px; padding: 16px; background: white; }
        .plugin-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
        .plugin-name { font-size: 15px; font-weight: 600; }
        .plugin-file { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
        .badge { font-size: 11px; padding: 2px 8px; border-radius: 20px; background: #f1f5f9; }
        .badge.enabled { background: #eef6ec; color: var(--success); }
        .badge.disabled { background: #fef2f0; color: var(--danger); }
        .plugin-actions { display: flex; gap: 8px; margin-top: 14px; }
        .plugin-actions .btn { flex: 1; justify-content: center; }
        
        .btn {
            padding: 6px 12px; font-size: 12px; font-weight: 500;
            border-radius: 6px; cursor: pointer; border: none;
            display: inline-flex; align-items: center; gap: 6px;
            text-decoration: none; transition: all 0.15s; white-space: nowrap;
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-secondary { background: #f1f5f9; color: var(--text-sub); border: 1px solid var(--border); }
        .btn-secondary:hover { background: #e9edf2; }
        .btn-success { background: var(--success); color: white; }
        .btn-success:hover { background: #235b23; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { background: #a83426; }
        .btn-warning { background: var(--warning); color: white; }
        .btn-warning:hover { background: #9e4a15; }
        
        .empty-state { text-align: center; padding: 48px 20px; color: var(--text-muted); }
        
        .modal {
            display: none; position: fixed; inset: 0;
            background: rgba(0, 0, 0, 0.4); z-index: 1000;
            align-items: center; justify-content: center; padding: 20px;
        }
        .modal-content {
            background: white; border-radius: 12px;
            width: 100%; max-width: 680px; max-height: 85vh;
            overflow-y: auto; overflow-x: hidden;
        }
        .modal-header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .modal-header h3 { font-size: 16px; font-weight: 600; }
        .close-btn { background: none; border: none; font-size: 18px; cursor: pointer; color: var(--text-muted); }
        .modal-body { padding: 20px; }
        .modal-footer { padding: 16px 20px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 12px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 500; color: var(--text-sub); margin-bottom: 6px; }
        .form-control, .form-textarea {
            width: 100%; padding: 10px 12px; font-size: 13px;
            border: 1px solid var(--border); border-radius: 8px;
            font-family: inherit; max-width: 100%;
        }
        .form-textarea { min-height: 400px; font-family: 'SF Mono', monospace; font-size: 12px; resize: vertical; }
        .form-control:focus, .form-textarea:focus { border-color: var(--primary); }
        .form-select {
            width: 100%; padding: 10px 12px; font-size: 13px;
            border: 1px solid var(--border); border-radius: 8px;
            font-family: inherit; background: white;
        }
        
        .mobile-header { display: none; padding: 12px 16px; background: white; border-bottom: 1px solid var(--border); align-items: center; justify-content: space-between; }
        .menu-toggle { background: none; border: none; font-size: 20px; cursor: pointer; }
        
        .notification {
            position: fixed; bottom: 20px; right: 20px;
            padding: 10px 16px; border-radius: 8px; font-size: 13px;
            background: var(--text-main); color: white; z-index: 1100;
            transform: translateX(120%); transition: transform 0.2s;
            max-width: calc(100vw - 40px);
        }
        .notification.show { transform: translateX(0); }
        .notification.success { background: var(--success); }
        .notification.error { background: var(--danger); }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.2s; z-index: 200; box-shadow: 2px 0 12px rgba(0,0,0,0.1); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .mobile-header { display: flex; }
            .top-bar { display: none; }
            .container { padding: 16px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .plugin-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="mobile-header">
        <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
        <span style="font-weight:500;">月下独酌管机</span>
        <div></div>
    </div>

    <div class="desktop-layout">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h1>月下独酌管机</h1>
                <p>机器人管理后台</p>
            </div>
            <nav class="sidebar-nav">
                <a href="main.php" class="nav-item"><i class="fas fa-tachometer-alt"></i> 总览</a>
                <a href="#" class="nav-item" id="navAddBot"><i class="fas fa-plus-circle"></i> 添加机器人</a>
                <a href="set.php" class="nav-item"><i class="fas fa-user-cog"></i> 账号设置</a>
                <a href="simulate.php" class="nav-item"><i class="fas fa-vial"></i> 指令测试</a>
                <a href="cmdtest.php" class="nav-item"><i class="fas fa-paper-plane"></i> 主动消息</a>
                <a href="custom_api.php" class="nav-item"><i class="fas fa-code-branch"></i> API插件生成器</a>
                <a href="doc.php" class="nav-item"><i class="fas fa-file-alt"></i> 开发文档</a>
            </nav>
            <div class="sidebar-footer">保留 1.0 原有逻辑 · 简洁商务版</div>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <div class="page-title">插件管理 · <?php echo htmlspecialchars($appid); ?></div>
                <div>
                    <a href="main.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> 返回后台</a>
                    <button class="btn btn-primary" id="addPluginBtn"><i class="fas fa-plus"></i> 添加插件</button>
                </div>
            </div>

            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card"><div class="stat-label">已启用</div><div class="stat-value" id="enabledCount">0</div></div>
                    <div class="stat-card"><div class="stat-label">未启用</div><div class="stat-value" id="disabledCount">0</div></div>
                    <div class="stat-card"><div class="stat-label">全部插件</div><div class="stat-value" id="allCount">0</div></div>
                    <div class="stat-card"><div class="stat-label">AppID</div><div class="stat-value" style="font-size:12px; word-break:break-all;"><?php echo htmlspecialchars($appid); ?></div></div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>插件列表</h2>
                        <div class="tabs">
                            <div class="tab active" data-tab="enabled">已启用</div>
                            <div class="tab" data-tab="disabled">未启用</div>
                            <div class="tab" data-tab="all">全部</div>
                        </div>
                    </div>
                    <div id="enabledPlugins" class="tab-content active"><div class="plugin-grid" id="enabledList"><div class="empty-state">加载中...</div></div></div>
                    <div id="disabledPlugins" class="tab-content"><div class="plugin-grid" id="disabledList"><div class="empty-state">加载中...</div></div></div>
                    <div id="allPlugins" class="tab-content"><div class="plugin-grid" id="allList"><div class="empty-state">加载中...</div></div></div>
                </div>
            </div>
        </main>
    </div>

    <!-- 添加机器人模态框 -->
    <div class="modal" id="addBotModal">
        <div class="modal-content" style="max-width:480px;">
            <div class="modal-header"><h3>添加机器人</h3><button class="close-btn" data-close="addBotModal">&times;</button></div>
            <form id="addBotForm">
                <div class="modal-body">
                    <div class="form-group"><label>AppID</label><input type="text" class="form-control" id="addAppid" required placeholder="请输入机器人 AppID"></div>
                    <div class="form-group"><label>Secret</label><input type="text" class="form-control" id="addSecret" required placeholder="请输入机器人 Secret"></div>
                    <div class="form-group"><label>环境</label><select class="form-select" id="addEnvironment"><option value="正式">正式环境</option><option value="沙箱">沙箱环境</option></select></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-close="addBotModal">取消</button><button type="submit" class="btn btn-primary">添加</button></div>
            </form>
        </div>
    </div>

    <!-- 添加插件模态框 -->
    <div class="modal" id="addModal">
        <div class="modal-content" style="max-width:480px;">
            <div class="modal-header"><h3>添加插件</h3><button class="close-btn" data-close="addModal">&times;</button></div>
            <div class="modal-body">
                <div class="form-group"><label>插件名称</label><input type="text" class="form-control" id="pluginName" placeholder="请输入插件名称，不用带 .php"></div>
            </div>
            <div class="modal-footer"><button class="btn btn-secondary" data-close="addModal">取消</button><button class="btn btn-primary" id="confirmAddBtn">确认添加</button></div>
        </div>
    </div>

    <!-- 编辑插件模态框 -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header"><h3>编辑插件</h3><button class="close-btn" data-close="editModal">&times;</button></div>
            <div class="modal-body">
                <div class="form-group"><label>插件名称</label><input type="text" class="form-control" id="editPluginName" readonly></div>
                <div class="form-group"><label>插件内容</label><textarea class="form-textarea" id="pluginContent"></textarea></div>
            </div>
            <div class="modal-footer"><button class="btn btn-secondary" data-close="editModal">取消</button><button class="btn btn-primary" id="savePluginBtn"><i class="fas fa-save"></i> 保存</button></div>
        </div>
    </div>

    <!-- 删除确认模态框 -->
    <div class="modal" id="confirmModal">
        <div class="modal-content" style="max-width:400px;">
            <div class="modal-header"><h3>确认删除</h3><button class="close-btn" data-close="confirmModal">&times;</button></div>
            <div class="modal-body" style="text-align:center;"><p>确定要删除插件 <strong id="deletePluginName"></strong> 吗？</p><p style="font-size:12px; color:var(--text-muted);">此操作不可恢复</p></div>
            <div class="modal-footer"><button class="btn btn-secondary" data-close="confirmModal">取消</button><button class="btn btn-danger" id="confirmDeleteBtn">确认删除</button></div>
        </div>
    </div>

    <div id="notification" class="notification"></div>

    <script>
        const appid = '<?php echo addslashes($appid); ?>';
        let currentEditingPlugin = null;
        let deleteTargetPlugin = null;

        function showMsg(text, isSuccess) {
            const el = document.getElementById('notification');
            el.textContent = text;
            el.className = 'notification ' + (isSuccess ? 'success' : 'error') + ' show';
            setTimeout(() => el.classList.remove('show'), 2500);
        }

        function closeModal(id) { document.getElementById(id).style.display = 'none'; }
        document.querySelectorAll('[data-close]').forEach(btn => btn.addEventListener('click', () => closeModal(btn.dataset.close)));
        window.addEventListener('click', e => { if (e.target.classList.contains('modal')) e.target.style.display = 'none'; });

        async function loadPlugins() {
            try {
                const [enabledRes, allRes] = await Promise.all([
                    fetch(`api/plugin.php?type=list&appid=${encodeURIComponent(appid)}`).then(r => r.json()),
                    fetch('api/plugin.php?type=filelist').then(r => r.json())
                ]);
                if (allRes.code !== 200) throw new Error('加载失败');
                const enabledPlugins = Object.keys(enabledRes || {});
                const allPlugins = allRes.list || [];
                const enabled = [], disabled = [];
                allPlugins.forEach(p => enabledPlugins.includes(p) ? enabled.push(p) : disabled.push(p));
                document.getElementById('enabledCount').textContent = enabled.length;
                document.getElementById('disabledCount').textContent = disabled.length;
                document.getElementById('allCount').textContent = allPlugins.length;
                renderPluginList(enabled, 'enabledList', true);
                renderPluginList(disabled, 'disabledList', false);
                renderPluginList(allPlugins, 'allList', null);
            } catch (err) { showMsg('加载失败', false); }
        }

        function renderPluginList(plugins, containerId, isEnabled) {
            const container = document.getElementById(containerId);
            if (!plugins.length) { container.innerHTML = '<div class="empty-state">暂无插件</div>'; return; }
            container.innerHTML = plugins.map(plugin => `
                <div class="plugin-card">
                    <div class="plugin-header">
                        <div><div class="plugin-name">${escapeHtml(plugin)}</div><div class="plugin-file">${plugin}.php</div></div>
                        <span class="badge ${isEnabled === true ? 'enabled' : isEnabled === false ? 'disabled' : ''}">${isEnabled === true ? '已启用' : isEnabled === false ? '未启用' : '插件'}</span>
                    </div>
                    <div class="plugin-actions">
                        ${isEnabled !== null ? (isEnabled ? `<button class="btn btn-warning toggle-plugin" data-plugin="${plugin}" data-action="disable"><i class="fas fa-toggle-off"></i> 禁用</button>` : `<button class="btn btn-success toggle-plugin" data-plugin="${plugin}" data-action="enable"><i class="fas fa-toggle-on"></i> 启用</button>`) : ''}
                        <button class="btn btn-secondary edit-plugin" data-plugin="${plugin}"><i class="fas fa-edit"></i> 编辑</button>
                        <button class="btn btn-danger delete-plugin" data-plugin="${plugin}"><i class="fas fa-trash"></i> 删除</button>
                    </div>
                </div>
            `).join('');
            container.querySelectorAll('.toggle-plugin').forEach(btn => btn.addEventListener('click', () => togglePlugin(btn.dataset.plugin, btn.dataset.action)));
            container.querySelectorAll('.edit-plugin').forEach(btn => btn.addEventListener('click', () => openEditModal(btn.dataset.plugin)));
            container.querySelectorAll('.delete-plugin').forEach(btn => btn.addEventListener('click', () => openDeleteModal(btn.dataset.plugin)));
        }

        async function togglePlugin(plugin, action) {
            const url = `api/plugin.php?type=${action === 'enable' ? 'open' : 'close'}&appid=${encodeURIComponent(appid)}&name=${encodeURIComponent(plugin)}`;
            try {
                const res = await fetch(url);
                const data = await res.json();
                if (data.code === 200) { showMsg(`${action === 'enable' ? '启用' : '禁用'}成功`, true); loadPlugins(); }
                else showMsg(data.msg || '操作失败', false);
            } catch (err) { showMsg('操作失败', false); }
        }

        async function openEditModal(plugin) {
            currentEditingPlugin = plugin;
            document.getElementById('editPluginName').value = plugin;
            document.getElementById('pluginContent').value = '加载中...';
            try {
                const res = await fetch(`api/plugin.php?type=read&name=${encodeURIComponent(plugin)}`);
                const data = await res.json();
                if (data.code === 200) document.getElementById('pluginContent').value = data.msg;
                else showMsg(data.msg || '读取失败', false);
                document.getElementById('editModal').style.display = 'flex';
            } catch (err) { showMsg('读取失败', false); }
        }

        document.getElementById('savePluginBtn').addEventListener('click', async () => {
            if (!currentEditingPlugin) return;
            const content = document.getElementById('pluginContent').value;
            try {
                const res = await fetch(`api/plugin.php?type=write&name=${encodeURIComponent(currentEditingPlugin)}`, {
                    method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ content })
                });
                const data = await res.json();
                if (data.code === 200) { showMsg('保存成功', true); closeModal('editModal'); loadPlugins(); }
                else showMsg(data.msg || '保存失败', false);
            } catch (err) { showMsg('保存失败', false); }
        });

        function openDeleteModal(plugin) {
            deleteTargetPlugin = plugin;
            document.getElementById('deletePluginName').textContent = plugin;
            document.getElementById('confirmModal').style.display = 'flex';
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
            if (!deleteTargetPlugin) return;
            try {
                const res = await fetch(`api/plugin.php?type=delete&name=${encodeURIComponent(deleteTargetPlugin)}`);
                const data = await res.json();
                if (data.code === 200) { showMsg('删除成功', true); closeModal('confirmModal'); loadPlugins(); }
                else showMsg(data.msg || '删除失败', false);
            } catch (err) { showMsg('删除失败', false); }
            deleteTargetPlugin = null;
        });

        document.getElementById('addPluginBtn').addEventListener('click', () => document.getElementById('addModal').style.display = 'flex');
        document.getElementById('confirmAddBtn').addEventListener('click', async () => {
            const name = document.getElementById('pluginName').value.trim();
            if (!name) { showMsg('请输入插件名称', false); return; }
            try {
                const res = await fetch(`api/plugin.php?type=add&name=${encodeURIComponent(name)}`);
                const data = await res.json();
                if (data.code === 200) { showMsg('添加成功', true); closeModal('addModal'); document.getElementById('pluginName').value = ''; loadPlugins(); }
                else showMsg(data.msg || '添加失败', false);
            } catch (err) { showMsg('添加失败', false); }
        });

        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                const tabId = tab.dataset.tab;
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                document.getElementById(tabId + 'Plugins').classList.add('active');
            });
        });

        // 添加机器人
        document.getElementById('navAddBot').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('addBotModal').style.display = 'flex';
        });

        document.getElementById('addBotForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const addAppid = document.getElementById('addAppid').value.trim();
            const addSecret = document.getElementById('addSecret').value.trim();
            const addEnv = document.getElementById('addEnvironment').value;
            if (!addAppid || !addSecret) { showMsg('请填写完整信息', false); return; }
            try {
                const res = await fetch(`api/bot.php?type=add&appid=${encodeURIComponent(addAppid)}&secret=${encodeURIComponent(addSecret)}&environment=${encodeURIComponent(addEnv)}`);
                const data = await res.json();
                if (data.code === 200) { showMsg('添加成功', true); closeModal('addBotModal'); document.getElementById('addBotForm').reset(); }
                else showMsg(data.msg || '添加失败', false);
            } catch (err) { showMsg('网络错误', false); }
        });

        function escapeHtml(str) { if (!str) return ''; return String(str).replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m])); }

        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        if (menuToggle) {
            menuToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
            document.addEventListener('click', (e) => {
                if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !menuToggle.contains(e.target)) sidebar.classList.remove('open');
            });
        }
        loadPlugins();
    </script>
</body>
</html>