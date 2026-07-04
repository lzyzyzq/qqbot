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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="referrer" content="no-referrer">
    <title>指令测试 · 月下独酌管机</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
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
            --danger: #c23d2e;
            --sidebar-width: 240px;
            --header-height: 52px;
        }
        body {
            background: var(--bg);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-main);
            line-height: 1.5;
            -webkit-text-size-adjust: 100%;
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
            padding: 28px 32px; max-width: 1200px; margin: 0 auto;
            overflow-x: hidden;
        }
        .card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 16px; padding: 20px; margin-bottom: 24px;
            overflow-x: hidden;
        }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: var(--text-sub); }
        .form-control, .form-select {
            width: 100%; padding: 10px 12px; border: 1px solid var(--border);
            border-radius: 10px; font-size: 14px; background: white;
            max-width: 100%;
        }
        textarea.form-control { resize: vertical; }
        .btn {
            padding: 8px 18px; border-radius: 10px; border: none;
            cursor: pointer; font-weight: 500;
            display: inline-flex; align-items: center; gap: 8px;
            white-space: nowrap;
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-secondary { background: #f1f5f9; color: var(--text-sub); border: 1px solid var(--border); }
        .replies-area {
            margin-top: 20px; border-top: 1px solid var(--border);
            padding-top: 16px; overflow-x: hidden;
        }
        .reply-item {
            background: #f8fafc; border-radius: 12px;
            padding: 14px; margin-bottom: 12px;
            border-left: 4px solid var(--primary);
            max-width: 100%; overflow-x: hidden;
        }
        .reply-meta {
            font-size: 11px; color: var(--text-muted);
            margin-bottom: 8px; display: flex; gap: 12px; flex-wrap: wrap;
        }
        .reply-content {
            max-width: 100%; overflow-x: hidden;
            word-wrap: break-word; overflow-wrap: break-word;
        }
        .reply-content img, .reply-content video, .reply-content audio {
            max-width: 100%; border-radius: 8px; margin-top: 6px;
        }
        .reply-content .markdown-body {
            background: transparent; padding: 0; font-size: 13px;
            max-width: 100%; overflow-x: hidden;
            word-wrap: break-word; overflow-wrap: break-word;
        }
        .reply-content .markdown-body * {
            max-width: 100%;
        }
        .empty-replies { text-align: center; color: var(--text-muted); padding: 24px; }
        
        .mobile-header { display: none; padding: 12px 16px; background: white; border-bottom: 1px solid var(--border); align-items: center; justify-content: space-between; }
        .menu-toggle { background: none; border: none; font-size: 20px; cursor: pointer; }
        
        .modal {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.4); z-index: 1000;
            align-items: center; justify-content: center; padding: 20px;
        }
        .modal-content {
            background: white; border-radius: 12px;
            width: 100%; max-width: 480px; max-height: 90vh;
            overflow-y: auto; overflow-x: hidden;
        }
        .modal-header { padding: 18px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .modal-header h3 { font-size: 16px; font-weight: 600; }
        .close-btn { background: none; border: none; font-size: 18px; cursor: pointer; color: var(--text-muted); padding: 4px; }
        .modal-body { padding: 20px; }
        .modal-footer { padding: 16px 20px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 12px; }
        
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
            .form-control, .form-select { font-size: 16px; }
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
            <a href="simulate.php" class="nav-item active"><i class="fas fa-vial"></i> 指令测试</a>
            <a href="cmdtest.php" class="nav-item"><i class="fas fa-paper-plane"></i> 主动消息</a>
            <a href="custom_api.php" class="nav-item"><i class="fas fa-code-branch"></i> API插件生成器</a>
            <a href="doc.php" class="nav-item"><i class="fas fa-file-alt"></i> 开发文档</a>
        </nav>
        <div class="sidebar-footer">保留 1.0 原有逻辑 · 简洁商务版</div>
    </aside>
    <main class="main-content">
        <div class="top-bar">
            <div class="page-title">指令测试</div>
            <a href="main.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> 返回后台</a>
        </div>
        <div class="container">
            <div class="card">
                <h3><i class="fas fa-vial"></i> 发送测试指令</h3>
                <p style="color: var(--text-muted); margin-bottom: 16px;">选择机器人，输入指令，系统将模拟真实用户触发机器人插件，并实时显示机器人回复（支持文字/图片/音频/视频/Markdown）。</p>
                <form id="simulateForm">
                    <div class="form-group">
                        <label>选择机器人</label>
                        <select id="appid" class="form-select" required><option value="">加载中...</option></select>
                    </div>
                    <div class="form-group">
                        <label>指令内容</label>
                        <textarea id="content" class="form-control" rows="3" placeholder="例如：统计"></textarea>
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <button type="submit" class="btn btn-primary" id="sendBtn"><i class="fas fa-paper-plane"></i> 发送指令</button>
                        <button type="button" class="btn btn-secondary" id="clearBtn"><i class="fas fa-eraser"></i> 清空</button>
                    </div>
                </form>
                <div id="repliesContainer" class="replies-area" style="display: none;">
                    <h4>🤖 机器人回复</h4>
                    <div id="repliesList"></div>
                </div>
            </div>
            <div class="card">
                <h3>📖 使用说明</h3>
                <ul style="margin-left: 20px; color: var(--text-sub);">
                    <li>模拟用户消息不会写入日志，仅用于测试插件响应。</li>
                    <li>支持显示机器人发送的 <strong>文字、图片、音频、视频、Markdown</strong> 内容。</li>
                    <li>无需填写用户ID和消息类型，系统会自动模拟私聊场景。</li>
                </ul>
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
                    <input type="text" class="form-control" id="addAppid" required placeholder="请输入机器人 AppID">
                </div>
                <div class="form-group">
                    <label>Secret</label>
                    <input type="text" class="form-control" id="addSecret" required placeholder="请输入机器人 Secret">
                </div>
                <div class="form-group">
                    <label>环境</label>
                    <select class="form-select" id="addEnvironment">
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

<div id="notification" class="notification"></div>

<script>
    const appidSelect = document.getElementById('appid');
    const content = document.getElementById('content');
    const sendBtn = document.getElementById('sendBtn');
    const repliesContainer = document.getElementById('repliesContainer');
    const repliesList = document.getElementById('repliesList');

    function showMsg(text, isSuccess) {
        const el = document.getElementById('notification');
        el.textContent = text;
        el.className = 'notification ' + (isSuccess ? 'success' : 'error') + ' show';
        setTimeout(() => el.classList.remove('show'), 2500);
    }

    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    document.querySelectorAll('[data-close]').forEach(btn => btn.addEventListener('click', () => closeModal(btn.dataset.close)));
    window.addEventListener('click', e => { if (e.target.classList.contains('modal')) e.target.style.display = 'none'; });

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m]));
    }

    function renderMarkdown(text) {
        if (!text) return '';
        try {
            return marked.parse(text);
        } catch(e) {
            return escapeHtml(text).replace(/\n/g, '<br>');
        }
    }

    function renderReply(reply) {
        const type = reply.type;
        const contentRaw = reply.content;
        let html = '';
        switch (type) {
            case 'image':
                html = `<img src="${escapeHtml(contentRaw)}" alt="图片" loading="lazy" style="max-width:100%; border-radius:8px;">`;
                break;
            case 'audio':
                html = `<audio controls style="width:100%;"><source src="${escapeHtml(contentRaw)}" type="audio/mpeg">您的浏览器不支持音频播放</audio>`;
                break;
            case 'video':
                html = `<video controls style="max-width:100%; border-radius:8px;"><source src="${escapeHtml(contentRaw)}" type="video/mp4">您的浏览器不支持视频播放</video>`;
                break;
            case 'md':
                html = `<div class="markdown-body">${renderMarkdown(contentRaw)}</div>`;
                break;
            default:
                html = `<div style="white-space: pre-wrap; word-break: break-word;">${escapeHtml(contentRaw).replace(/\n/g, '<br>')}</div>`;
        }
        return `
            <div class="reply-item">
                <div class="reply-meta">
                    <span><i class="fas fa-${type === 'image' ? 'image' : type === 'audio' ? 'music' : type === 'video' ? 'video' : 'comment'}"></i> 类型: ${type}</span>
                    <span><i class="fas fa-bullseye"></i> 目标: ${escapeHtml(reply.target)}</span>
                </div>
                <div class="reply-content">${html}</div>
            </div>
        `;
    }

    async function loadBots() {
        try {
            const res = await fetch('api/info.php?type=list');
            const bots = await res.json();
            if (!Array.isArray(bots) || bots.length === 0) {
                appidSelect.innerHTML = '<option value="">暂无机器人，请先添加</option>';
                return;
            }
            let options = '';
            bots.forEach(bot => {
                options += `<option value="${escapeHtml(bot.appid)}">${escapeHtml(bot.name || bot.appid)} (${escapeHtml(bot.type)})</option>`;
            });
            appidSelect.innerHTML = options;
        } catch(err) {
            appidSelect.innerHTML = '<option value="">加载失败</option>';
            showMsg('加载机器人列表失败', false);
        }
    }

    document.getElementById('simulateForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const appid = appidSelect.value;
        if (!appid) { showMsg('请选择机器人', false); return; }
        const msgContent = content.value.trim();
        if (!msgContent) { showMsg('请填写指令内容', false); return; }

        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 发送中...';
        repliesContainer.style.display = 'none';

        try {
            const payload = { appid, content: msgContent };
            const res = await fetch('api/simulate.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const text = await res.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch(e) {
                throw new Error('响应不是有效JSON: ' + text.substring(0, 200));
            }
            if (data.code === 200 && data.replies && data.replies.length) {
                repliesList.innerHTML = data.replies.map(renderReply).join('');
                repliesContainer.style.display = 'block';
                showMsg(`测试成功，机器人回复 ${data.replies.length} 条消息`, true);
            } else if (data.code === 200) {
                repliesList.innerHTML = '<div class="empty-replies">机器人没有返回任何内容</div>';
                repliesContainer.style.display = 'block';
                showMsg('指令已发送，但无回复', false);
            } else {
                showMsg(data.msg || '处理失败', false);
                repliesContainer.style.display = 'none';
            }
        } catch (err) {
            showMsg('请求失败：' + err.message, false);
            repliesContainer.style.display = 'none';
        } finally {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> 发送指令';
        }
    });

    document.getElementById('clearBtn').addEventListener('click', () => {
        content.value = '';
        repliesContainer.style.display = 'none';
    });

    // 添加机器人
    document.getElementById('navAddBot').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('addModal').style.display = 'flex';
    });

    document.getElementById('addForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const addAppid = document.getElementById('addAppid').value.trim();
        const addSecret = document.getElementById('addSecret').value.trim();
        const addEnv = document.getElementById('addEnvironment').value;
        if (!addAppid || !addSecret) { showMsg('请填写完整信息', false); return; }
        try {
            const res = await fetch(`api/bot.php?type=add&appid=${encodeURIComponent(addAppid)}&secret=${encodeURIComponent(addSecret)}&environment=${encodeURIComponent(addEnv)}`);
            const data = await res.json();
            if (data.code === 200) { showMsg('添加成功', true); closeModal('addModal'); document.getElementById('addForm').reset(); loadBots(); }
            else showMsg(data.msg || '添加失败', false);
        } catch (err) { showMsg('网络错误', false); }
    });

    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    if (menuToggle) {
        menuToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !menuToggle.contains(e.target)) sidebar.classList.remove('open');
        });
    }

    loadBots();
</script>
</body>
</html>