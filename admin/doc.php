<?php
require dirname(__DIR__) . '/function/Parsedown.php';
$parsedown = new Parsedown();
$parsedown->setMarkupEscaped(true);
$parsedown->setBreaksEnabled(true);
$markdown = file_get_contents(dirname(__DIR__) . '/文档.md');
$html = $parsedown->text($markdown);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>月下独酌管机 · 开发文档</title>
    <link rel="stylesheet" href="assets/markdown.css">
    <link rel="stylesheet" href="assets/highlight/default.min.css">
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
            --danger: #c23d2e;
            --danger-hover: #a83426;
            --success: #2c6e2c;
            --code-bg: #f1f5f9;
            --sidebar-width: 240px;
            --header-height: 52px;
        }

        body {
            background: var(--bg);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-main);
            line-height: 1.6;
            /* 关键修复：防止body溢出 */
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
            display: flex;
            flex-direction: column;
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
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            /* 关键修复：限制主内容区宽度 */
            min-width: 0;
            overflow-x: hidden;
        }
        .top-bar {
            background: var(--card); border-bottom: 1px solid var(--border);
            padding: 0 32px; height: var(--header-height);
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 10;
        }
        .page-title { font-size: 15px; font-weight: 500; }
        
        .container {
            padding: 28px 32px;
            /* 关键修复：限制容器宽度 */
            max-width: 100%;
            overflow-x: hidden;
        }
        
        .card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 10px; overflow: hidden;
        }
        .card-header { padding: 16px 20px; border-bottom: 1px solid var(--border); }
        .card-header h2 { font-size: 16px; font-weight: 600; }
        .card-header p { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
        
        .card-body {
            padding: 24px;
            /* 关键修复：防止内容溢出 */
            overflow-x: hidden;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* ========== Markdown 内容溢出修复核心 ========== */
        .markdown-body {
            font-size: 14px;
            line-height: 1.7;
            /* 关键修复：强制换行 */
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
            /* 限制最大宽度 */
            max-width: 100%;
            overflow-x: hidden;
        }

        /* 所有元素都不允许超出容器 */
        .markdown-body * {
            max-width: 100%;
            box-sizing: border-box;
        }

        /* 图片自适应 */
        .markdown-body img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        /* 标题 */
        .markdown-body h1 {
            font-size: 24px; margin: 24px 0 16px;
            padding-bottom: 8px; border-bottom: 1px solid var(--border);
            word-wrap: break-word;
        }
        .markdown-body h2 {
            font-size: 20px; margin: 20px 0 12px;
            padding-bottom: 6px; border-bottom: 1px solid var(--border);
            word-wrap: break-word;
        }
        .markdown-body h3 {
            font-size: 18px; margin: 18px 0 10px;
            word-wrap: break-word;
        }
        .markdown-body p {
            margin: 0 0 16px; color: var(--text-sub);
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* 代码块 - 允许横向滚动但不超出容器 */
        .markdown-body pre {
            background: var(--code-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 14px;
            margin: 16px 0;
            /* 关键修复：限制宽度并允许滚动 */
            max-width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            /* 防止代码块本身溢出 */
            white-space: pre;
            word-wrap: normal;
            word-break: normal;
        }

        /* 代码块内的代码 */
        .markdown-body pre code {
            background: none;
            padding: 0;
            font-family: 'SF Mono', Monaco, 'Cascadia Code', monospace;
            font-size: 13px;
            /* 保持代码不换行，依赖 pre 的滚动 */
            white-space: pre;
            word-wrap: normal;
            word-break: normal;
        }

        /* 行内代码 */
        .markdown-body code {
            background: var(--code-bg);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'SF Mono', Monaco, monospace;
            font-size: 13px;
            /* 行内代码允许换行 */
            word-wrap: break-word;
            word-break: break-all;
        }

        /* 表格 - 包裹层处理 */
        .markdown-body table {
            border-collapse: collapse;
            margin: 16px 0;
            /* 表格自适应 */
            width: auto;
            max-width: 100%;
            display: table;
        }
        .markdown-body th, .markdown-body td {
            border: 1px solid var(--border);
            padding: 8px 12px;
            text-align: left;
            /* 单元格内容换行 */
            word-wrap: break-word;
            word-break: break-word;
        }
        .markdown-body th {
            background: #f8fafc;
            font-weight: 600;
            white-space: nowrap;
        }

        /* 列表 */
        .markdown-body ul, .markdown-body ol {
            margin: 0 0 16px 24px;
            padding-left: 0;
        }
        .markdown-body li {
            margin: 4px 0;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* 链接 - 长链接换行 */
        .markdown-body a {
            word-break: break-all;
            word-wrap: break-word;
        }

        /* 引用块 */
        .markdown-body blockquote {
            border-left: 3px solid var(--border);
            padding: 8px 16px;
            margin: 16px 0;
            color: var(--text-sub);
            word-wrap: break-word;
        }

        /* 按钮 */
        .btn {
            padding: 6px 14px; font-size: 13px; font-weight: 500;
            border-radius: 6px; cursor: pointer; border: none;
            display: inline-flex; align-items: center; gap: 6px;
            text-decoration: none; transition: all 0.15s;
            white-space: nowrap;
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-secondary { background: #f1f5f9; color: var(--text-sub); border: 1px solid var(--border); }
        .btn-secondary:hover { background: #e9edf2; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { background: var(--danger-hover); }

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
        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block; font-size: 13px; font-weight: 500;
            color: var(--text-sub); margin-bottom: 6px;
        }
        .form-control, .form-select {
            width: 100%; padding: 8px 12px; font-size: 13px;
            border: 1px solid var(--border); border-radius: 6px;
            font-family: inherit;
        }
        .form-control:focus, .form-select:focus { border-color: var(--primary); }

        /* 通知 */
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

        /* 移动端 */
        .mobile-header {
            display: none; padding: 12px 16px; background: white;
            border-bottom: 1px solid var(--border);
            align-items: center; justify-content: space-between;
        }
        .menu-toggle { background: none; border: none; font-size: 20px; cursor: pointer; }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%); transition: transform 0.2s;
                z-index: 200; box-shadow: 2px 0 12px rgba(0,0,0,0.1);
            }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .mobile-header { display: flex; }
            .top-bar { display: none; }
            .container { padding: 12px; }
            .card-body { padding: 12px; }
            .markdown-body { font-size: 13px; }
            .markdown-body pre { font-size: 12px; padding: 10px; }
            .markdown-body h1 { font-size: 20px; }
            .markdown-body h2 { font-size: 17px; }
            .markdown-body h3 { font-size: 15px; }
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
                <a href="doc.php" class="nav-item active"><i class="fas fa-file-alt"></i> 开发文档</a>
            </nav>
            <div class="sidebar-footer">保留 1.0 原有逻辑 · 简洁商务版</div>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <div class="page-title">开发文档</div>
                <div>
                    <a href="main.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> 返回后台</a>
                    <a href="../文档.md" class="btn btn-primary" target="_blank"><i class="fas fa-external-link-alt"></i> 原文</a>
                </div>
            </div>

            <div class="container">
                <div class="card">
                    <div class="card-header">
                        <h2>文档内容</h2>
                        <p>支持代码高亮、表格滚动和移动端阅读</p>
                    </div>
                    <div class="card-body">
                        <div class="markdown-body">
                            <?= $html ?>
                        </div>
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

    <div id="notification" class="notification"></div>

    <script src="assets/highlight/highlight.min.js"></script>
    <script src="assets/highlight/php.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 代码高亮
            document.querySelectorAll('pre code').forEach(el => {
                try {
                    hljs.highlightElement(el);
                } catch (e) {
                    console.warn('代码高亮失败:', e);
                }
            });

            // 表格溢出处理 - 包裹在可滚动的 div 中
            document.querySelectorAll('.markdown-body table').forEach(table => {
                // 避免重复包裹
                if (table.parentElement.classList.contains('table-wrapper')) return;
                
                const wrapper = document.createElement('div');
                wrapper.className = 'table-wrapper';
                wrapper.style.cssText = 'max-width:100%; overflow-x:auto; margin:16px 0; -webkit-overflow-scrolling:touch;';
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            });

            // 强制所有图片添加 max-width
            document.querySelectorAll('.markdown-body img').forEach(img => {
                img.style.maxWidth = '100%';
                img.style.height = 'auto';
            });

            // 移动端菜单
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            if (menuToggle && sidebar) {
                menuToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
                document.addEventListener('click', (e) => {
                    if (window.innerWidth <= 768 && 
                        !sidebar.contains(e.target) && 
                        !menuToggle.contains(e.target)) {
                        sidebar.classList.remove('open');
                    }
                });
            }

            // ---------- 添加机器人相关 ----------
            function showMsg(text, isSuccess) {
                const el = document.getElementById('notification');
                if (!el) return;
                el.textContent = text;
                el.className = 'notification ' + (isSuccess ? 'success' : 'error') + ' show';
                setTimeout(() => el.classList.remove('show'), 2500);
            }

            function closeModal(id) {
                const modal = document.getElementById(id);
                if (modal) modal.style.display = 'none';
            }

            document.querySelectorAll('[data-close]').forEach(btn => {
                btn.addEventListener('click', () => closeModal(btn.dataset.close));
            });

            window.addEventListener('click', e => {
                if (e.target.classList.contains('modal')) {
                    e.target.style.display = 'none';
                }
            });

            // 打开添加模态框
            const navAddBot = document.getElementById('navAddBot');
            if (navAddBot) {
                navAddBot.addEventListener('click', function(e) {
                    e.preventDefault();
                    const modal = document.getElementById('addModal');
                    if (modal) modal.style.display = 'flex';
                });
            }

            // 提交添加机器人
            const addForm = document.getElementById('addForm');
            if (addForm) {
                addForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const appid = document.getElementById('appid')?.value.trim();
                    const secret = document.getElementById('secret')?.value.trim();
                    const env = document.getElementById('environment')?.value || '正式';
                    
                    if (!appid || !secret) {
                        showMsg('请填写完整信息', false);
                        return;
                    }

                    try {
                        const res = await fetch(`api/bot.php?type=add&appid=${encodeURIComponent(appid)}&secret=${encodeURIComponent(secret)}&environment=${encodeURIComponent(env)}`);
                        const data = await res.json();
                        if (data.code === 200) {
                            showMsg('添加成功', true);
                            closeModal('addModal');
                            addForm.reset();
                        } else {
                            showMsg(data.msg || '添加失败', false);
                        }
                    } catch (err) {
                        showMsg('网络错误', false);
                    }
                });
            }
        });
    </script>
</body>
</html>