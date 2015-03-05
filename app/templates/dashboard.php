<?php
/**
 * Dashboard page
 */
$this->layout('layouts::main', ['requiredCssFiles' => ['dashboard', 'jqueryui', 'jhtmlarea']]);
?>
<div id="add-edit-form">
    <form>
        Title: <input type="text" id="log-title" value="" size="60"><br><br>
        <textarea id="log-body" cols="80" rows="10"></textarea><br>
        <div>Category: <span id="categories"></span></div>
    </form>
    <div id="messages"></div>
</div>

<button type="button" class="section-title disabled" id="show-cheesto-button">Show Message Center</button>

<section id="messages-panel" class="messages-panel">
    <?php if ($showCheesto): ?>
        <div class="messages-cheesto">
            <div class="messages-cheesto-header">
                <span class="messages-title">Ĉeesto</span>

                <div class="messages-controls">
                    <select id="status-select">
                        <option value="-1">Set Status:</option>
                    </select>
                </div>
            </div>

            <div id="cheesto-status-table" class="messages-cheesto-content"></div>
        </div>
    <?php endif; ?>

    <div class="messages-msg">
        <div class="messages-msg-header">
            <span class="messages-title">Messages</span>

            <div class="messages-controls">
                <button class="button" id="new-msg-button">New</button>
            </div>
        </div>

        <div id="msgs-table" class="messages-msg-content"></div>
    </div>
</section>


<button type="button" class="section-title disabled" id="show-logs-button">Show Logs</button>

<section id="logs-panel" class="logs-panel">
    <div class="control-panel">
        <div class="search-console">
            <input type="search" class="query-box" id="search-query" placeholder="Search" value="" autocomplete="off">
            <button class="search-button" type="button" id="search-btn">Search</button>
        </div>

        <div class="top-controls">
            <form>
                <button type="button" class="button" id="prev-page-button">Prev</button>
                <?= $createButton ?>
                <button type="button" class="button button-right" id="next-page-button">Next</button>
                <button type="button" class="button button-right" id="clear-search-button">Clear Search</button>
            </form>
        </div>
    </div>

    <?php if ($showLog): ?>


        <div id="log-list">Loading logs...</div>
    <?php endif; ?>
</section>

<?= $this->loadJS(['jquery', 'jqueryui', 'jhtmlarea', 'common', 'categories', 'dashboard']) ?>
