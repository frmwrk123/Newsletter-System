{include file='header'}
{include file="wysiwyg"}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/TabbedPane.class.js"></script>

<div class="mainHeadline">
    {* <img src="{@RELATIVE_WCF_DIR}icon/group{@$action|ucfirst}L.png" alt="" /> *}
    <div class="headlineContainer">
        <h2>{lang}wcf.acp.newsletter.{@$action}{/lang}</h2>
    </div>
</div>

{if $errorField}
    <p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $result|isset && $result == "success"}
    <p class="success">{lang}wcf.acp.newsletter.{@$action}.success{/lang}</p>
{/if}

<div class="contentHeader">
    <div class="largeButtons">
        <ul>
            <li><a href="index.php?page=NewsletterList&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wcf.acp.menu.link.content.newsletter.newsletterList{/lang}">{*<img src="{@RELATIVE_WCF_DIR}icon/groupM.png" alt="" />*} <span>{lang}wcf.acp.menu.link.content.newsletter.newsletterList{/lang}</span></a></li>
            {if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
        </ul>
    </div>
</div>

<form method="post" action="index.php?form=Newsletter{@$action|ucfirst}">
    <div class="border content">
        <div class="container-1">
            <fieldset>
                <legend>{lang}wcf.acp.newsletter.general{/lang}</legend>
                
                <div class="formElement{if $errorType.subject|isset} formError{/if}" id="subjectDiv">
                    <div class="formFieldLabel">
                        <label for="subject">{lang}wcf.acp.newsletter.subject{/lang}</label>
                    </div>
                    <div class="formField">
                        <input type="text" class="inputText" id="subject" name="subject" value="{$subject}" />
                        {if $errorType.subject|isset}
                            <p class="innerError">
                                {if $errorType.subject == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
                                {if $errorType.subject == 'tooShort'}{lang}wcf.acp.newsletter.subject.error.tooShort{/lang}{/if}
                            </p>
                        {/if}
                    </div>
                    <div class="formFieldDesc hidden" id="subjectHelpMessage">
                        <p>{lang}wcf.acp.newsletter.subject.description{/lang}</p>
                    </div>
                </div>
                <script type="text/javascript">//<![CDATA[
                    inlineHelp.register('subject');
                //]]></script>
                <div class="formGroup{if $errorType.date|isset} formError{/if}" id="dateDiv">
                    <div class="formGroupLabel">
                        <label for="date">{lang}wcf.acp.newsletter.date{/lang}</label>
                    </div>
                    <div class="formGroupField">
                    	<fieldset>
                    		<legend>{lang}wcf.acp.newsletter.date{/lang}</legend>
                    		<div class="floatContainer">
								<div class="floatedElement">
									<select name="day">
										<option value="">{lang}wcf.acp.newsletter.date.day{/lang}</option>
										{foreach from=$dateOptions.day item=dayNr}
											<option value="{@$dayNr}"{if $day == $dayNr} selected="selected"{/if}>{@$dayNr}</option>
										{/foreach}
									</select>
								</div>
								<div class="floatedElement">
									<select name="month">
										<option value="">{lang}wcf.acp.newsletter.date.month{/lang}</option>
										{foreach from=$dateOptions.month item=monthNr}
											<option value="{@$monthNr}"{if $month == $monthNr} selected="selected"{/if}>{@$monthNr}</option>
										{/foreach}
									</select>
								</div>
								<div class="floatedElement">
									<select name="year">
										<option value="">{lang}wcf.acp.newsletter.date.year{/lang}</option>
										{foreach from=$dateOptions.year item=yearNr}
											<option value="{@$yearNr}"{if $year == $yearNr} selected="selected"{/if}>{@$yearNr}</option>
										{/foreach}
									</select>
								</div>
								{if $errorField == 'date'}
									<p class="innerError">
										{if $errorType == 'notValidated'}{lang}wcf.acp.newsletter.date.error.notValidated{/lang}{/if}
									</p>
								{/if}
							</div>
                    	</fieldset>
                	</div>
                </div>
                <script type="text/javascript">//<![CDATA[
                    inlineHelp.register('date');
                //]]></script>
            </fieldset>
            <fieldset>
            	<legend>{lang}wcf.acp.newsletter.message{/lang}</legend>
            	<div class="editorFrame formElement{if $errorField == 'text'} formError{/if}" id="textDiv">
            	    <div class="formFieldLabel">
                        <label for="text">{lang}wcf.acp.newsletter.text{/lang}</label>
                    </div>
                    <div class="formField">
                        <textarea name="text" id="text" rows="15" cols="40" tabindex="{counter name='tabindex'}">{$text}</textarea>
                        {if $errorType.text|isset}
                            <p class="innerError">
                                {if $errorType.text == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
                            </p>
                        {/if}
                    </div>
                    <div class="formFieldDesc hidden" id="textHelpMessage">
                        <p>{lang}wcf.acp.newsletter.text.description{/lang}</p>
                    </div>
                </div>
                {include file='messageFormTabs'}
                <script type="text/javascript">//<![CDATA[
                    inlineHelp.register('text');
                //]]></script>
            </fieldset>
        </div>
    </div>
    
    <div class="formSubmit">
        <input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" tabindex="{counter name='tabindex'}" />
		<input type="reset" name="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" tabindex="{counter name='tabindex'}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
        {@SID_INPUT_TAG}
        <input type="hidden" name="action" value="{@$action}" />
    </div>
</form>

{include file='footer'}