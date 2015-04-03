<?php
/**
 * Dashboard page
 */
$this->layout('layouts::main', ['requiredCssFiles' => ['dashboard', 'jqueryui', 'jhtmlarea', 'datetimepicker']]);
?>
<div id="add-edit-form" class="hidden-dialog">
    <form>
        Title: <input type="text" id="log-title" value="" size="60"><br><br>
        <textarea id="log-body" cols="80" rows="10"></textarea><br>
        <div>Category: <span id="categories"></span></div>
    </form>
    <div id="messages"></div>
</div>

<div id="cheesto-status-form" class="hidden-dialog">
    <form>
        <fieldset class="outer">
            Time Quick Set:
            <table>
                <tr>
                    <td>10 Minutes<input type="radio" name="quicktime" onClick="Cheesto.setDateTime(10);"></td>
                    <td>20 Minutes<input type="radio" name="quicktime" onClick="Cheesto.setDateTime(20);"></td>
                </tr>
                <tr>
                    <td>30 Minutes<input type="radio" name="quicktime" onClick="Cheesto.setDateTime(30);"></td>
                    <td>40 Minutes<input type="radio" name="quicktime" onClick="Cheesto.setDateTime(40);"></td>
                </tr>
                <tr>
                    <td>50 Minutes<input type="radio" name="quicktime" onClick="Cheesto.setDateTime(50);"></td>
                    <td>1 Hour<input type="radio" name="quicktime" onClick="Cheesto.setDateTime(60);">
                </tr>
                <tr>
                    <td>1 Hour 15 Min.<input type="radio" name="quicktime" onClick="Cheesto.setDateTime(75);"></td>
                    <td>1 Hour 30 Min.<input type="radio" name="quicktime" onClick="Cheesto.setDateTime(90);"></td>
                </tr>
                <tr>
                    <td>1 Hour 45 Min.<input type="radio" name="quicktime" onClick="Cheesto.setDateTime(105);"></td>
                    <td>2 Hours<input type="radio" name="quicktime" onClick="Cheesto.setDateTime(120);"></td>
                </tr>
            </table>
        </fieldset>

        <fieldset class="outer">
            <fieldset>
                <label for="cheesto-date-pick">Return Time:</label>
                <input type="text" id="cheesto-date-pick" value="Today">
            </fieldset>
            <fieldset>
                <label for="cheesto-message-text">Message:</label>
                <textarea id="cheesto-message-text" cols="25" rows="10"></textarea>
            </fieldset>
        </fieldset>
    </form>
</div>

<button type="button" class="section-title disabled" id="show-cheesto-button">Show Message Center</button>

<section id="messages-panel" class="messages-panel">
    <?php if ($showCheesto): ?>
        <div id="messages-cheesto">
            <div id="messages-cheesto-header">
                <span class="messages-title">Ĉeesto</span>

                <div class="messages-controls">
                    <select id="status-select">
                        <option value="-1">Set Status:</option>
                    </select>
                </div>
            </div>

            <div id="messages-cheesto-content">Loading...</div>
        </div>
    <?php endif; ?>
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

<?= $this->loadJS(['jquery', 'jqueryui', 'jhtmlarea', 'timepicker', 'common', 'categories', 'cheesto', 'dashboard']) ?>
