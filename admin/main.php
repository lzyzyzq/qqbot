<?php
if (!isset($_COOKIE['admin_token'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>月下独酌管机 · 管理后台</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }
        :focus {
            outline: none;
        }

        :root {
            --bg: #f8fafc;
            --card: #ffffff;
            --border: #e9edf2;
            --text-main: #1a2c3e;
            --text-sub: #5e6f8d;
            --text-muted: #8b9ab0;
            --primary: #2c6b9e;
            --primary-hover: #235b87;
            --danger: #c23d2e;
            --danger-hover: #a83426;
            --success: #2c6e2c;
            --sidebar-width: 240px;
            --header-height: 52px;
        }

        body {
            background: var(--bg);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-main);
            line-height: 1.5;
        }

        .desktop-layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: var(--card);
            border-right: 1px solid var(--border);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-header h1 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-main);
        }

        .sidebar-header p {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 0;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 24px;
            color: var(--text-sub);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.15s;
            cursor: pointer;
        }

        .nav-item:hover {
            background: #f1f5f9;
            color: var(--primary);
        }

        .nav-item.active {
            background: #f1f5f9;
            color: var(--primary);
            border-left: 3px solid var(--primary);
            padding-left: 21px;
        }

        .nav-item i {
            width: 20px;
            font-size: 15px;
        }

        .sidebar-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            font-size: 11px;
            color: var(--text-muted);
        }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .top-bar {
            background: var(--card);
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .page-title {
            font-size: 15px;
            font-weight: 500;
            color: var(--text-main);
        }

        .top-actions {
            display: flex;
            gap: 12px;
        }

        .container {
            padding: 28px 32px;
            max-width: 1400px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 16px 20px;
        }

        .stat-label {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 600;
            color: var(--text-main);
            line-height: 1.2;
        }

        .stat-hint {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 6px;
        }

        .section-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
        }

        .section-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-main);
        }

        .section-sub {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .btn {
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 500;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: var(--text-sub);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: #e9edf2;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: var(--danger-hover);
        }

        .btn-sm {
            padding: 4px 10px;
            font-size: 12px;
        }

        .bots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 16px;
            padding: 20px;
        }

        .bot-card {
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 16px;
            background: white;
        }

        .bot-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .bot-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid var(--border);
            background: #f8fafc;
        }

        .bot-info h4 {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 2px;
        }

        .bot-info p {
            font-size: 11px;
            color: var(--text-muted);
            word-break: break-all;
        }

        .bot-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .env-badge {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 20px;
            background: #f1f5f9;
            color: var(--text-sub);
        }

        .bot-stats {
            display: flex;
            gap: 12px;
            border-top: 1px solid var(--border);
            padding-top: 12px;
            margin-bottom: 14px;
        }

        .stat-item {
            flex: 1;
            text-align: center;
        }

        .stat-item .num {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-main);
        }

        .stat-item .label {
            font-size: 10px;
            color: var(--text-muted);
        }

        .bot-actions {
            display: flex;
            gap: 10px;
            border-top: 1px solid var(--border);
            padding-top: 12px;
        }

        .bot-actions .btn {
            flex: 1;
            justify-content: center;
            padding: 6px 0;
        }

        .empty-state {
            text-align: center;
            padding: 48px 20px;
            color: var(--text-muted);
            font-size: 14px;
        }

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            width: 100%;
            max-width: 480px;
            max-height: 90vh;
            overflow: auto;
        }

        .modal-header {
            padding: 18px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-size: 16px;
            font-weight: 600;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: var(--text-muted);
            padding: 4px;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            padding: 16px 20px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-sub);
            margin-bottom: 6px;
        }

        .form-control, .form-select {
            width: 100%;
            padding: 8px 12px;
            font-size: 13px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-family: inherit;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
        }

        .mobile-header {
            display: none;
            padding: 12px 16px;
            background: white;
            border-bottom: 1px solid var(--border);
            align-items: center;
            justify-content: space-between;
        }

        .menu-toggle {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: var(--text-main);
        }

        /* 移动端溢出修复 */
        img {
            max-width: 100%;
            height: auto;
        }
        pre, code, .raw-box, .markdown-body table, .reply-content {
            max-width: 100%;
            overflow-x: auto;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .card-body {
            overflow-x: auto;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.2s;
                z-index: 200;
                box-shadow: 2px 0 12px rgba(0,0,0,0.1);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .mobile-header {
                display: flex;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            .container {
                padding: 20px 16px;
            }
            .top-bar {
                display: none;
            }
            .bots-grid {
                grid-template-columns: 1fr;
            }
            .card-body {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        .notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 13px;
            background: var(--text-main);
            color: white;
            z-index: 1100;
            transform: translateX(120%);
            transition: transform 0.2s;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification.success {
            background: var(--success);
        }

        .notification.error {
            background: var(--danger);
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
                <a href="main.php" class="nav-item active"><i class="fas fa-tachometer-alt"></i> 总览</a>
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
                <div class="page-title">机器人管理</div>
                <div class="top-actions">
                    <button class="btn btn-secondary btn-sm" id="refreshTopBtn"><i class="fas fa-sync-alt"></i> 刷新</button>
                    <button class="btn btn-primary btn-sm" id="addTopBtn"><i class="fas fa-plus"></i> 添加</button>
                </div>
            </div>

            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card"><div class="stat-label">机器人总数</div><div class="stat-value" id="statTotal">0</div><div class="stat-hint">已接入</div></div>
                    <div class="stat-card"><div class="stat-label">今日群聊</div><div class="stat-value" id="statGroup">0</div><div class="stat-hint">聚合统计</div></div>
                    <div class="stat-card"><div class="stat-label">今日私聊</div><div class="stat-value" id="statPrivate">0</div><div class="stat-hint">聚合统计</div></div>
                    <div class="stat-card"><div class="stat-label">今日加群</div><div class="stat-value" id="statJoin">0</div><div class="stat-hint">聚合统计</div></div>
                </div>

                <div class="section-card">
                    <div class="section-header">
                        <div>
                            <div class="section-title">机器人列表</div>
                            <div class="section-sub">每个机器人独立管理插件和日志</div>
                        </div>
                        <button class="btn btn-secondary" id="refreshListBtn"><i class="fas fa-sync-alt"></i> 刷新</button>
                    </div>
                    <div class="bots-grid" id="botsGrid">
                        <div class="empty-state">加载中...</div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- 添加机器人模态框 -->
    <div class="modal" id="addModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>添加机器人</h3>
                <button class="close-btn" data-close="addModal">&times;</button>
            </div>
            <form id="addForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>AppID</label>
                        <input type="text" class="form-control" id="appid" required placeholder="请输入机器人 AppID">
                    </div>
                    <div class="form-group">
                        <label>Secret</label>
                        <input type="text" class="form-control" id="secret" required placeholder="请输入机器人 Secret">
                    </div>
                    <div class="form-group">
                        <label>环境</label>
                        <select class="form-select" id="environment">
                            <option value="正式">正式环境</option>
                            <option value="沙箱">沙箱环境</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-close="addModal">取消</button>
                    <button type="submit" class="btn btn-primary">添加</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 删除确认模态框 -->
    <div class="modal" id="confirmModal">
        <div class="modal-content" style="max-width:380px;">
            <div class="modal-header">
                <h3>确认删除</h3>
                <button class="close-btn" data-close="confirmModal">&times;</button>
            </div>
            <div class="modal-body" style="text-align:center;">
                <i class="fas fa-exclamation-triangle" style="font-size:32px; color:#c23d2e; margin-bottom:12px; display:block;"></i>
                <p style="margin-bottom:8px;">确定删除该机器人吗？</p>
                <p style="font-size:12px; color:#8b9ab0;">此操作不可恢复</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-close="confirmModal">取消</button>
                <button class="btn btn-danger" id="confirmDeleteBtn">确认删除</button>
            </div>
        </div>
    </div>

    <div id="notification" class="notification"></div>

    <script>
        let currentBots = [];
        let deleteTargetAppid = null;

        function showMsg(text, isSuccess) {
            const el = document.getElementById('notification');
            el.textContent = text;
            el.className = 'notification ' + (isSuccess ? 'success' : 'error') + ' show';
            setTimeout(() => el.classList.remove('show'), 2500);
        }

        function closeModal(id) { document.getElementById(id).style.display = 'none'; }

        document.querySelectorAll('[data-close]').forEach(btn => btn.addEventListener('click', () => closeModal(btn.dataset.close)));
        window.addEventListener('click', e => { if (e.target.classList.contains('modal')) e.target.style.display = 'none'; });

        async function fetchBots() {
            try {
                const res = await fetch('api/info.php?type=list');
                const data = await res.json();
                currentBots = Array.isArray(data) ? data : [];
                renderStats();
                renderBots();
            } catch (err) {
                document.getElementById('botsGrid').innerHTML = '<div class="empty-state">加载失败，请刷新重试</div>';
                showMsg('加载机器人列表失败', false);
            }
        }

        function renderStats() {
            let total = currentBots.length;
            let group = 0, privateChat = 0, join = 0;
            currentBots.forEach(b => {
                group += Number(b.data?.群聊 || 0);
                privateChat += Number(b.data?.私聊 || 0);
                join += Number(b.data?.加群 || 0);
            });
            document.getElementById('statTotal').textContent = total;
            document.getElementById('statGroup').textContent = group;
            document.getElementById('statPrivate').textContent = privateChat;
            document.getElementById('statJoin').textContent = join;
        }

        function renderBots() {
            const grid = document.getElementById('botsGrid');
            if (!currentBots.length) {
                grid.innerHTML = '<div class="empty-state">暂无机器人，点击“添加机器人”开始接入</div>';
                return;
            }
            grid.innerHTML = currentBots.map(bot => `
                <div class="bot-card" data-appid="${escapeHtml(bot.appid)}">
                    <div class="bot-header">
                        <img src="${escapeHtml(bot.avatar) || 'https://via.placeholder.com/44'}" class="bot-avatar" onerror="this.src='https://via.placeholder.com/44'">
                        <div class="bot-info">
                            <h4>${escapeHtml(bot.name || '未命名机器人')}</h4>
                            <p>${escapeHtml(bot.appid || '')}</p>
                        </div>
                    </div>
                    <div class="bot-meta">
                        <span class="env-badge">${escapeHtml(bot.type || '正式')}</span>
                    </div>
                    <div class="bot-stats">
                        <div class="stat-item"><div class="num">${bot.data?.群聊 || 0}</div><div class="label">群聊</div></div>
                        <div class="stat-item"><div class="num">${bot.data?.私聊 || 0}</div><div class="label">私聊</div></div>
                        <div class="stat-item"><div class="num">${bot.data?.加群 || 0}</div><div class="label">加群</div></div>
                    </div>
                    <div class="bot-actions">
                        <a href="plugin.php?appid=${encodeURIComponent(bot.appid)}" class="btn btn-secondary btn-sm"><i class="fas fa-puzzle-piece"></i> 插件</a>
                        <a href="log.php?appid=${encodeURIComponent(bot.appid)}" class="btn btn-secondary btn-sm"><i class="fas fa-chart-line"></i> 日志</a>
                        <button class="btn btn-danger btn-sm delete-bot" data-appid="${escapeHtml(bot.appid)}"><i class="fas fa-trash"></i> 删除</button>
                    </div>
                </div>
            `).join('');
            document.querySelectorAll('.delete-bot').forEach(btn => btn.addEventListener('click', (e) => {
                e.stopPropagation();
                deleteTargetAppid = btn.dataset.appid;
                document.getElementById('confirmModal').style.display = 'flex';
            }));
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
            if (!deleteTargetAppid) return;
            try {
                const res = await fetch(`api/bot.php?type=del&appid=${encodeURIComponent(deleteTargetAppid)}`);
                const data = await res.json();
                if (data.code === 200) { showMsg('删除成功', true); closeModal('confirmModal'); fetchBots(); }
                else showMsg(data.msg || '删除失败', false);
            } catch (err) { showMsg('网络错误', false); }
            deleteTargetAppid = null;
        });

        document.getElementById('addForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const appid = document.getElementById('appid').value.trim();
            const secret = document.getElementById('secret').value.trim();
            const env = document.getElementById('environment').value;
            if (!appid || !secret) { showMsg('请填写完整信息', false); return; }
            try {
                const res = await fetch(`api/bot.php?type=add&appid=${encodeURIComponent(appid)}&secret=${encodeURIComponent(secret)}&environment=${encodeURIComponent(env)}`);
                const data = await res.json();
                if (data.code === 200) { showMsg('添加成功', true); closeModal('addModal'); document.getElementById('addForm').reset(); fetchBots(); }
                else showMsg(data.msg || '添加失败', false);
            } catch (err) { showMsg('网络错误', false); }
        });

        document.getElementById('addTopBtn').addEventListener('click', () => document.getElementById('addModal').style.display = 'flex');
        document.getElementById('navAddBot').addEventListener('click', (e) => { e.preventDefault(); document.getElementById('addModal').style.display = 'flex'; });
        document.getElementById('refreshListBtn').addEventListener('click', fetchBots);
        document.getElementById('refreshTopBtn').addEventListener('click', fetchBots);

        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        if (menuToggle) {
            menuToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
            document.addEventListener('click', (e) => {
                if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !menuToggle.contains(e.target)) sidebar.classList.remove('open');
            });
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m]));
        }

        fetchBots();
    </script>
</body>
</html>