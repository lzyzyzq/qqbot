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
    <title>主动消息 · 月下独酌管机</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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
            overflow-x: hidden;
            width: 100%;
        }

        .desktop-layout { display: flex; min-height: 100vh; }
        .sidebar {
            width: var(--sidebar-width);
            background: var(--card);
            border-right: 1px solid var(--border);
            position: fixed;
            top: 0; bottom: 0; left: 0;
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
        .nav-item.active {
            background: #f1f5f9; color: var(--primary);
            border-left: 3px solid var(--primary); padding-left: 21px;
        }
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
            padding: 28px 32px; max-width: 1000px;
            overflow-x: hidden;
        }
        .card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 10px; overflow: hidden; margin-bottom: 20px;
        }
        .card-header { padding: 16px 20px; border-bottom: 1px solid var(--border); }
        .card-header h2 { font-size: 16px; font-weight: 600; margin-bottom: 4px; }
        .card-header p { font-size: 12px; color: var(--text-muted); }
        .card-body {
            padding: 20px;
            overflow-x: hidden;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 500; color: var(--text-sub); margin-bottom: 6px; }
        .form-control, .form-select {
            width: 100%; padding: 10px 12px; font-size: 14px;
            border: 1px solid var(--border); border-radius: 8px;
            font-family: inherit; background: var(--card);
            max-width: 100%;
        }
        .form-control:focus, .form-select:focus { border-color: var(--primary); }
        
        .btn {
            padding: 8px 16px; font-size: 13px; font-weight: 500;
            border-radius: 6px; cursor: pointer; border: none;
            display: inline-flex; align-items: center; gap: 6px;
            transition: all 0.15s; white-space: nowrap;
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-secondary { background: #f1f5f9; color: var(--text-sub); border: 1px solid var(--border); }
        .btn-secondary:hover { background: #e9edf2; }
        
        .result-box {
            background: #f1f5f9; border-radius: 8px; padding: 14px;
            font-family: monospace; font-size: 13px;
            white-space: pre-wrap; word-break: break-all;
            margin-top: 20px; display: none;
            max-width: 100%; overflow-x: auto;
        }
        .result-box.show { display: block; }
        .result-success { border-left: 4px solid var(--success); }
        .result-error { border-left: 4px solid var(--danger); }
        
        .mobile-header {
            display: none; padding: 12px 16px; background: white;
            border-bottom: 1px solid var(--border);
            align-items: center; justify-content: space-between;
        }
        .menu-toggle { background: none; border: none; font-size: 20px; cursor: pointer; }

        /* 模态框 */
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
        .modal-header {
            padding: 18px 20px; border-bottom: 1px solid var(--border);
            display: flex; justify-content: space-between; align-items: center;
        }
        .modal-header h3 { font-size: 16px; font-weight: 600; }
        .close-btn {
            background: none; border: none; font-size: 18px;
            cursor: pointer; color: var(--text-muted); padding: 4px;
        }
        .modal-body { padding: 20px; }
        .modal-footer {
            padding: 16px 20px; border-top: 1px solid var(--border);
            display: flex; justify-content: flex-end; gap: 12px;
        }

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

        /* 移动端溢出修复 */
        img { max-width: 100%; height: auto; }
        pre, code, .raw-box { max-width: 100%; overflow-x: auto; white-space: pre-wrap; word-break: break-word; }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%); transition: transform 0.2s;
                z-index: 200; box-shadow: 2px 0 12px rgba(0,0,0,0.1);
            }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .mobile-header { display: flex; }
            .top-bar { display: none; }
            .container { padding: 16px; }
            .card-body { overflow-x: auto; -webkit-overflow-scrolling: touch; padding: 16px; }
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
                <a href="cmdtest.php" class="nav-item active"><i class="fas fa-paper-plane"></i> 主动消息</a>
                <a href="custom_api.php" class="nav-item"><i class="fas fa-code-branch"></i> API插件生成器</a>
                <a href="doc.php" class="nav-item"><i class="fas fa-file-alt"></i> 开发文档</a>
            </nav>
            <div class="sidebar-footer">保留 1.0 原有逻辑 · 简洁商务版</div>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <div class="page-title">主动消息</div>
                <a href="main.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> 返回后台</a>
            </div>

            <div class="container">
                <div class="card">
                    <div class="card-header">
                        <h2>发送主动消息</h2>
                        <p>向指定的用户或群聊发送消息，用于触发插件逻辑</p>
                    </div>
                    <div class="card-body">
                        <form id="testForm">
                            <div class="form-group">
                                <label>选择机器人 <span style="color:var(--danger);">*</span></label>
                                <select class="form-select" id="appid" required><option value="">加载中...</option></select>
                            </div>
                            <div class="form-group">
                                <label>消息类型 <span style="color:var(--danger);">*</span></label>
                                <select class="form-select" id="targetType">
                                    <option value="c2c">私聊 (C2C)</option>
                                    <option value="group">群聊 (Group)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>目标ID <span style="color:var(--danger);">*</span></label>
                                <input type="text" class="form-control" id="targetId" placeholder="例如：用户 openid 或群 openid" required>
                                <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">可从日志中获取 openid（如 d.author.id 或 d.group_openid）</div>
                            </div>
                            <div class="form-group">
                                <label>消息内容 <span style="color:var(--danger);">*</span></label>
                                <textarea class="form-control" id="content" rows="3" placeholder="例如：/ping 或 任意测试文字" required></textarea>
                            </div>
                            <div style="display: flex; gap: 12px;">
                                <button type="submit" class="btn btn-primary" id="sendBtn"><i class="fas fa-paper-plane"></i> 发送消息</button>
                                <button type="button" class="btn btn-secondary" id="clearBtn"><i class="fas fa-eraser"></i> 清空</button>
                            </div>
                        </form>
                        <div id="resultBox" class="result-box"></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>使用说明</h2>
                        <p>如何获取目标ID（openid）</p>
                    </div>
                    <div class="card-body">
                        <ul style="margin-left:20px; color:var(--text-sub); font-size:13px; word-wrap:break-word;">
                            <li>进入对应机器人的 <strong>日志管理</strong> → 点击任意一条消息记录 → 在"原始数据"中查找 <code>d.author.id</code>（用户openid）或 <code>d.group_openid</code>（群openid）。</li>
                            <li>私聊消息需要用户的 <code>openid</code>，群聊消息需要群的 <code>group_openid</code>。</li>
                            <li>发送后机器人会立即向目标推送消息，若插件正确监听，应做出相应回复。</li>
                        </ul>
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
        const targetType = document.getElementById('targetType');
        const targetId = document.getElementById('targetId');
        const content = document.getElementById('content');
        const sendBtn = document.getElementById('sendBtn');
        const resultBox = document.getElementById('resultBox');
        const form = document.getElementById('testForm');

        function showMsg(text, isSuccess) {
            const el = document.getElementById('notification');
            el.textContent = text;
            el.className = 'notification ' + (isSuccess ? 'success' : 'error') + ' show';
            setTimeout(() => el.classList.remove('show'), 2500);
        }

        function showResult(message, isError = false) {
            resultBox.innerHTML = message;
            resultBox.className = 'result-box show ' + (isError ? 'result-error' : 'result-success');
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.style.display = 'none';
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
            } catch (err) {
                appidSelect.innerHTML = '<option value="">加载失败</option>';
                showMsg('加载机器人列表失败', false);
            }
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const appid = appidSelect.value;
            if (!appid) { showMsg('请选择机器人', false); return; }
            const type = targetType.value;
            const target_id = targetId.value.trim();
            if (!target_id) { showMsg('请填写目标ID', false); return; }
            const msgContent = content.value.trim();
            if (!msgContent) { showMsg('请填写消息内容', false); return; }

            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 发送中...';
            showResult('发送请求中...', false);

            try {
                const res = await fetch('api/sendmsg.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ appid, type, target_id, content: msgContent })
                });
                if (!res.ok) throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                const text = await res.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    throw new Error(`响应不是有效 JSON: ${text.substring(0, 200)}`);
                }
                if (data.code === 200) {
                    showResult(`✅ 发送成功！\n消息ID：${data.msg_id || '无'}\n\n机器人已向目标推送消息，请观察插件是否正常响应。`, false);
                    showMsg('发送成功', true);
                } else {
                    showResult(`❌ 发送失败\n错误信息：${data.msg || '未知错误'}`, true);
                    showMsg(data.msg || '发送失败', false);
                }
            } catch (err) {
                showResult(`❌ 请求失败：${err.message}`, true);
                showMsg('请求失败：' + err.message, false);
            } finally {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> 发送消息';
            }
        });

        document.getElementById('clearBtn').addEventListener('click', () => {
            targetId.value = '';
            content.value = '';
            showResult('', false);
            resultBox.classList.remove('show');
        });

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[m]));
        }

        // 添加机器人模态框事件
        document.querySelectorAll('[data-close]').forEach(btn => {
            btn.addEventListener('click', () => closeModal(btn.dataset.close));
        });
        window.addEventListener('click', e => {
            if (e.target.classList.contains('modal')) e.target.style.display = 'none';
        });

        const navAddBot = document.getElementById('navAddBot');
        if (navAddBot) {
            navAddBot.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('addModal').style.display = 'flex';
            });
        }

        const addForm = document.getElementById('addForm');
        if (addForm) {
            addForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const addAppid = document.getElementById('addAppid').value.trim();
                const addSecret = document.getElementById('addSecret').value.trim();
                const addEnv = document.getElementById('addEnvironment').value;
                if (!addAppid || !addSecret) {
                    showMsg('请填写完整信息', false);
                    return;
                }
                try {
                    const res = await fetch(`api/bot.php?type=add&appid=${encodeURIComponent(addAppid)}&secret=${encodeURIComponent(addSecret)}&environment=${encodeURIComponent(addEnv)}`);
                    const data = await res.json();
                    if (data.code === 200) {
                        showMsg('添加成功', true);
                        closeModal('addModal');
                        addForm.reset();
                        loadBots();
                    } else {
                        showMsg(data.msg || '添加失败', false);
                    }
                } catch (err) {
                    showMsg('网络错误', false);
                }
            });
        }

        // 移动端菜单
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        if (menuToggle) {
            menuToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
            document.addEventListener('click', (e) => {
                if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            });
        }

        loadBots();
    </script>
</body>
</html>