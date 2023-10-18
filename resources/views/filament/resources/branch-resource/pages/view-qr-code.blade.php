<div style="
    margin: auto;
    width: 50%;
">
    {{ QrCode::size(200)->generate($record->asset_code) }}
</div>
