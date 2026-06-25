<div class="adm-card">
    @if($contacts->isEmpty())
    <div class="adm-empty">
        <div class="adm-empty-state">
            <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p>No hay mensajes de contacto.</p>
        </div>
    </div>
    @else
    <div class="rp-table-wrap">
        <table class="rp-table">
            <thead>
                <tr>
                    <th class="rp-th">Remitente</th>
                    <th class="rp-th">Email</th>
                    <th class="rp-th rp-th--left">Mensaje</th>
                    <th class="rp-th rp-th--center">Estado</th>
                    <th class="rp-th rp-th--center">Fecha</th>
                    <th class="rp-th rp-th--center" data-orderable="false">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contacts as $contact)
                <tr class="rp-tr {{ !$contact->readed ? 'rp-tr--unread' : '' }}">
                    <td class="rp-td">
                        <div class="rp-user">
                            <div class="rp-avatar rp-avatar--initials">{{ strtoupper(substr($contact->name, 0, 2)) }}</div>
                            <div class="rp-user-info">
                                <span class="rp-user-name">{{ $contact->name }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="rp-td">
                        <a href="mailto:{{ $contact->email }}" class="rp-email-link">{{ $contact->email }}</a>
                    </td>
                    <td class="rp-td rp-td--left">
                        <div class="rp-reason">{{ $contact->message }}</div>
                    </td>
                    <td class="rp-td rp-td--center">
                        @if($contact->readed)
                        <span class="rp-status rp-status--read">Leído</span>
                        @else
                        <span class="rp-status rp-status--unread">Sin leer</span>
                        @endif
                    </td>
                    <td class="rp-td rp-td--center">
                        <time class="rp-date" datetime="{{ $contact->created_at->toDateString() }}">
                            {{ $contact->created_at->diffForHumans() }}
                        </time>
                    </td>
                    <td class="rp-td rp-td--center">
                        <div class="rp-actions">
                            @if(!$contact->readed)
                            <form method="POST" action="{{ route('admin.reports.contact.read', $contact) }}" class="rp-inline-form">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="rp-btn rp-btn--ghost" title="Marcar como leído">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Leído
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('admin.reports.contact.unread', $contact) }}" class="rp-inline-form">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="rp-btn rp-btn--ghost" title="Marcar como no leído">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    No leído
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
