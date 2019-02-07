{%extends 'content_crm/member/layout.html'%}


{%block pageContent%}


{{block('pageSteps')}}
<div class="f">
 <div class="cauto s12 pA5">
{{block("psidebar")}}
 </div>
 <div class="c s12 pA5">

  <form class="cbox" method="post" autocomplete="off">

    {{block("iPermission")}}

    {{block("iLanguage")}}
    {{block("iUsername")}}
    {{block("iTwoStep")}}
    {{block("iRememberme")}}
    {{block("iSidebar")}}

    {{block("iStatus")}}

    {%if perm('cpUsersPasswordChange')%}
      {{block('iPassword')}}
      {{block('irePassword')}}
    {%endif%}


    {{block("iSave")}}

    {%if perm_su()%}
      <div class="badge mT10 mB10 danger" data-kerkere-icon data-kerkere='.DeleteUserYN'>{%trans "Delete user"%}</div>
      <div class="DeleteUserYN" data-kerkere-content="hide">
        <div class="msg danger">
          {%trans "Are you sure to delete this user?"%}
          <span data-confirm data-data='{"deleteuser" : "DeleteUserYN"}' class="badge warn floatL">{%trans "Delete"%}</span>
        </div>
      </div>
    {%endif%}
  </form>


    {%if perm_su()%}
      <div class="cbox">
        <form method="post">

            {%if chatIdList%}
              <div class="msg success2">
                {%trans "User have chatid"%}
              {%for key, value in chatIdList%}
                <br>
                <span class="badge mLR20">{{(key + 1) | fitNumber}}</span><b>{{value.chatid | fitNumber(false)}}</b>
                <span data-confirm data-data='{"removechatid" : "removechatid", "chatid" : "{{value.chatid}}"}' class="badge danger floatL">{%trans "Remove chatid"%}</span>
              {%endfor%}
              </div>

            {%endif%}

            <input type="hidden" name="setChatid" value="1">
            <div class="input">
              <label class="addon" for="ichatid">{%trans "chatid"%} {%trans "Telegram"%}</label>
              <input type="number" id="ichatid" name="chatid">
              <button class="btn addon primary">{%trans "Add"%}</button>
            </div>
          </div>
        </form>


    {%if androidList%}
      <div class="cbox">
        <form method="post">
            <div class="msg success2">
              {%trans "User have android"%}
              {%for key, value in androidList%}
                <span class="badge mLR20">{{(key + 1) | fitNumber}}</span><b>{{value | dump}}</b>
              {%endfor%}
            </div>
          </div>
        </form>
    {%endif%}

    {%endif%}

  {{block('sessionList')}}
  {{block('logList')}}
 </div>
</div>
{%endblock%}

{%block iLanguage%}
<div class="mTB10">
  <label for="language">{%trans "Default language"%}</label>
  <select name="language" class="ui dropdown" id="language">
    <option value="" readonly>{%trans "Select one item"%}</option>
    {%for key, lang in lang.list%}
      <option value="{{key}}" {%if dataRowMember.language == key%} selected {%endif%} >{{lang}}</option>
    {%endfor%}
  </select>
</div>
{%endblock%}




{%block iPermission%}

{%if permGroup%}

  <div class="mT10">
    <label for="permission">{%trans "Permission"%}</label>
    <select name="permission" class="ui dropdown" id="permission">
      <option value="" readonly>{%trans "No permission"%}</option>
      <option value="0" readonly>{%trans "No permission"%}</option>
      {%for key, value in permGroup%}
        <option value="{{key}}" {%if dataRowMember.permission == key%} selected {%endif%} >{%set myValue = value.title%}{%trans myValue%}</option>
      {%endfor%}
    </select>
  </div>

{%endif%}

{%endblock%}




{%block iUsername%}
<label for="username">{%trans "Username"%}</label>
<div class="input ltr">
  <input type="text" name="username" id="username" placeholder='{%trans "Username"%}' value="{{dataRowMember.username}}" maxlength='40' minlength="1" pattern=".{1,40}" title='{%trans "Enter a valid username from 3 to 40 character"%}' autofocus>
</div>
{%endblock%}


{%block iPassword%}
<label for="password">{%trans "Password"%} <small>{%trans "Enter to change pass"%}</small></label>
<div class="input">
  <input type="password" name="password" id="password" placeholder='{%if dataRowMember.password%}{%trans "Password was set, enter to change it!"%}{%else%}{%trans "Password not set, enter  to set it!"%}{%endif%}' maxlength='50' data-response-realtime autocomplete="new-password">
</div>
{%endblock%}


{%block irePassword%}
<div data-response='password' data-response-hide data-response-effect='slide'>
  <label for="repassword">{%trans "Confirm password"%} <small class="fc-red">* {%trans "Require to change current password"%}</small></label>
  <div class="input">
  <input type="password" name="repassword" id="repassword" placeholder='{%if dataRowMember.password%}{%trans "Password was set, enter to change it!"%}{%else%}{%trans "Password not set, enter  to set it!"%}{%endif%}'  maxlength='50'>
</div>
</div>
{%endblock%}


{%block iSidebar%}
<div class="switch1 mTB0">
  <input type="checkbox" name="sidebar" id="xsidebar" {%if dataRowMember.sidebar %}checked{%endif%}>
  <label for="xsidebar"></label>
  <label for="xsidebar">{%trans "Show sidebar"%}</label>
</div>
{%endblock%}

{%block iStatus%}
<div class="mT10">
  <label for="status">{%trans "Status"%}</label>
  <select name="status" class="ui dropdown" id="status">
    <option value="" readonly>{%trans "Select one item"%} *</option>
    <option value="active" {% if dataRowMember.status == 'active' %} selected {%endif%}>{%trans "Active"%}</option>
    <option value="awaiting" {% if dataRowMember.status == 'awaiting' %} selected {%endif%}>{%trans "Awaiting"%}</option>
    <option value="deactive" {% if dataRowMember.status == 'deactive' %} selected {%endif%}>{%trans "Deactive"%}</option>
    <option value="removed" {% if dataRowMember.status == 'removed' %} selected {%endif%}>{%trans "Removed"%}</option>
    <option value="filter" {% if dataRowMember.status == 'filter' %} selected {%endif%}>{%trans "Filter"%}</option>
    <option value="unreachable" {% if dataRowMember.status == 'unreachable' %} selected {%endif%}>{%trans "Unreachable"%}</option>
  </select>
</div>
{%endblock%}


{%block iRememberme%}
<div class="switch1 mT20">
 <input type="checkbox" name="forceremember" id="forceremember" {%if dataRowMember.forceremember  %}checked{%endif%}>
 <label for="forceremember"></label>
 <label for="forceremember">{%trans "Save remember session"%}</label>
</div>
{%endblock%}


{%block iTwoStep%}
<div class="switch1 mT20">
 <input type="checkbox" name="twostep" id="twostep" {%if dataRowMember.twostep  %}checked{%endif%}>
 <label for="twostep"></label>
 <label for="twostep">{%trans "Two step verification"%}</label>
</div>
{%endblock%}




{%block iSave%}
<button class="btn primary block mT20">{%trans "Save"%}</button>
{%endblock%}





{%block sessionList%}
<div class="cbox">

  <h4>{%trans "Active sessions"%} </h4>
  {%for key, row in sessionsList%}
  <div class="msg">
      <div class="badge warn" title='{%trans "Browser"%}'>{{row.browser}} {{row.browserVer | fitNumber}}</div>
      <div class="badge" title='{%trans "Operation System"%}'>{{row.os}} {{row.osVer | fitNumber}}</div>
      <div class="badge" title='{%trans "IP"%}'>{{row.ip}}</div>
      <div class="badge primary" title='{%trans "Date login"%}'>{{row.last | dt('human')}}</div>
      {%if row.code == currentCookie %}
      <div class="badge success">{%trans "Current session"%}</div>
      {%endif%}
      <a class="badge floatL danger" href="{{url.pwd}}" data-ajaxify data-method="post" data-data='{"id" : "{{row.id}}", "type": "terminate" }' tabindex='-1'>{%trans "Terminate"%}</a>
  </div>

{%endfor%}
</div>

{%endblock%}




{%use 'content_crm/log/home/logTable.html'%}



{%block logList%}

{%if dataTable%}

  {{block('htmlTable')}}
  {%if dataFilter%}
    {{block('htmlFilter')}}
  {%endif%}

{%endif%}


{%endblock%}